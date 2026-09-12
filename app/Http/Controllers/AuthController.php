<?php

namespace App\Http\Controllers;

use App\Models\BeauticianProfile;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    const MAX_ATTEMPTS = 5;
    const LOCK_MINUTES = 5;
    const OTP_EXPIRY_MINUTES = 10;

    /* ------------------------------------------------------------------
     |  LOGIN
     | ------------------------------------------------------------------ */

    public function showLogin()
    {
        if (Auth::check()) {
            return $this->redirectBasedOnRole(Auth::user());
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if (!$user) {
            return back()->withInput()->with('error', 'Email not found.');
        }

        if ($user->isLocked()) {
            $minutes = Carbon::now()->diffInMinutes($user->locked_until) + 1;
            return back()->withInput()->with('error', "Too many failed attempts. Please try again in {$minutes} minute(s), or reset your password.");
        }

        if (Hash::check($credentials['password'], $user->password)) {
            // Admin accounts cannot sign in via public login
            if ($user->role === 'admin') {
                return back()->withInput()->with('error', 'Incorrect password.');
            }

            if (!$user->is_active) {
                return back()->withInput()->with('error', 'This account has been deactivated. Please contact support.');
            }

            // Reset failed attempts
            $user->failed_attempts = 0;
            $user->locked_until    = null;
            $user->save();

            Auth::login($user, $request->filled('remember'));
            $request->session()->regenerate();

            return $this->redirectBasedOnRole($user);
        }

        // Invalid password
        $user->failed_attempts += 1;

        if ($user->failed_attempts >= self::MAX_ATTEMPTS) {
            $user->locked_until = Carbon::now()->addMinutes(self::LOCK_MINUTES);
            $user->save();
            return back()->withInput()->with('error', 'Too many failed attempts. Your account is locked for ' . self::LOCK_MINUTES . ' minutes.');
        } else {
            $user->save();
            $remaining = self::MAX_ATTEMPTS - $user->failed_attempts;
            return back()->withInput()->with('error', "Incorrect password. {$remaining} attempt(s) remaining before your account is temporarily locked.");
        }
    }

    /* ------------------------------------------------------------------
     |  ADMIN LOGIN
     | ------------------------------------------------------------------ */

    public function showAdminLogin()
    {
        if (Auth::check() && Auth::user()->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }
        return view('auth.admin-login');
    }

    public function adminLogin(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $credentials['email'])->where('role', 'admin')->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return back()->withInput()->with('error', 'Invalid admin credentials.');
        }

        if (!$user->is_active) {
            return back()->withInput()->with('error', 'This admin account is deactivated.');
        }

        $user->failed_attempts = 0;
        $user->locked_until    = null;
        $user->save();

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('admin.dashboard');
    }

    /* ------------------------------------------------------------------
     |  REGISTRATION — Step 1: Collect info & send OTP via email
     | ------------------------------------------------------------------ */

    public function showRegister()
    {
        if (Auth::check()) {
            return $this->redirectBasedOnRole(Auth::user());
        }
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name'                  => 'required|string|max:100',
            'email'                 => 'required|email|max:100|unique:users,email',
            'password'              => 'required|string|min:6|confirmed',
            'role'                  => 'required|in:customer,beautician',
        ]);

        $otp = $this->generateOtp();

        if (!$this->sendOtpEmail($validated['email'], $otp)) {
            return back()->withInput()->with('error', 'Could not send the OTP email. Please check the address and try again.');
        }

        // Store pending registration in session (not in DB yet)
        session([
            'pending_registration' => [
                'name'     => $validated['name'],
                'email'    => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role'     => $validated['role'],
            ],
            'registration_otp'         => $otp,
            'registration_otp_expires' => Carbon::now()->addMinutes(self::OTP_EXPIRY_MINUTES)->timestamp,
        ]);

        return redirect()->route('register.otp')
            ->with('otp_sent', true)
            ->with('pending_email', $validated['email']);
    }

    /* ------------------------------------------------------------------
     |  REGISTRATION — Step 2: Verify OTP
     | ------------------------------------------------------------------ */

    public function showOtpVerification()
    {
        if (!session('pending_registration')) {
            return redirect()->route('register')->with('error', 'Please complete the registration form first.');
        }
        return view('auth.verify-otp');
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|string|size:6',
        ]);

        $pending    = session('pending_registration');
        $storedOtp  = session('registration_otp');
        $expiresAt  = session('registration_otp_expires');

        if (!$pending || !$storedOtp) {
            return redirect()->route('register')->with('error', 'Session expired. Please start the registration again.');
        }

        // Check expiry
        if (Carbon::now()->timestamp > $expiresAt) {
            session()->forget(['pending_registration', 'registration_otp', 'registration_otp_expires']);
            return redirect()->route('register')->with('error', 'The OTP has expired. Please register again.');
        }

        // Check OTP match
        if ($request->otp !== $storedOtp) {
            return back()->with('error', 'Incorrect OTP. Please check the code sent to your email and try again.');
        }

        // OTP is valid — create the user now
        $user = User::create([
            'name'            => $pending['name'],
            'email'           => $pending['email'],
            'password'        => $pending['password'],
            'role'            => $pending['role'],
            'is_active'       => true,
            'failed_attempts' => 0,
        ]);

        if ($user->role === 'beautician') {
            BeauticianProfile::create([
                'user_id'  => $user->id,
                'bio'      => '',
                'location' => '',
            ]);
        }

        // Clear session
        session()->forget(['pending_registration', 'registration_otp', 'registration_otp_expires']);

        return redirect()->route('login')->with('success', 'Account created successfully! Your email has been verified. Please sign in.');
    }

    public function resendOtp(Request $request)
    {
        $pending = session('pending_registration');

        if (!$pending) {
            return redirect()->route('register')->with('error', 'Session expired. Please start the registration again.');
        }

        $otp = $this->generateOtp();

        if (!$this->sendOtpEmail($pending['email'], $otp)) {
            return back()->with('error', 'Could not resend the OTP email. Please try again.');
        }

        session([
            'registration_otp'         => $otp,
            'registration_otp_expires' => Carbon::now()->addMinutes(self::OTP_EXPIRY_MINUTES)->timestamp,
        ]);

        return back()
            ->with('success', 'A new OTP has been sent to your email.')
            ->with('pending_email', $pending['email']);
    }

    /* ------------------------------------------------------------------
     |  FORGOT / RESET PASSWORD
     | ------------------------------------------------------------------ */

    public function forgotPassword()
    {
        return view('auth.forgot-password');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        $email = trim($request->email);

        $user = User::where('email', $email)->first();

        if (!$user) {
            return back()->withInput()->with('error', 'This email is not registered with us.');
        }

        $token          = bin2hex(random_bytes(32));
        $user->reset_token   = $token;
        $user->reset_expires = Carbon::now()->addHour();
        $user->save();

        $resetUrl = route('password.reset', ['token' => $token]);

        try {
            Mail::send([], [], function ($message) use ($user, $resetUrl) {
                $message->to($user->email, $user->name)
                    ->subject('Reset your BookMyBeauty password')
                    ->html("
                        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #eee; border-radius: 8px;'>
                            <h2 style='color: #1a1a2e; margin-bottom: 20px;'>BookMyBeauty Password Reset</h2>
                            <p>Hi <strong>" . htmlspecialchars($user->name) . "</strong>,</p>
                            <p>We received a request to reset your password. Click the button below to choose a new one (valid for 1 hour):</p>
                            <p style='margin: 25px 0;'>
                                <a href='{$resetUrl}' style='background: #1a1a2e; color: #c9a96e; padding: 12px 24px; text-decoration: none; border-radius: 6px; font-weight: bold; display: inline-block;'>Reset Password</a>
                            </p>
                            <p style='color: #777; font-size: 13px;'>If the button does not work, copy and paste this link into your browser:<br><a href='{$resetUrl}'>{$resetUrl}</a></p>
                            <hr style='border: none; border-top: 1px solid #eee; margin: 20px 0;'>
                            <p style='color: #999; font-size: 12px;'>If you did not request a password reset, you can safely ignore this email.</p>
                        </div>
                    ");
            });
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Could not send the reset email. Please try again later.');
        }

        return back()->with('message', 'A password reset link has been sent to your email.');
    }

    public function showResetPassword(string $token)
    {
        $user = User::where('reset_token', $token)
            ->where('reset_expires', '>', Carbon::now())
            ->first();

        if (!$user) {
            return redirect()->route('password.request')->with('error', 'This password reset link is invalid or has expired.');
        }

        return view('auth.reset-password', ['token' => $token, 'email' => $user->email]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token'    => 'required',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = User::where('reset_token', $request->token)
            ->where('reset_expires', '>', Carbon::now())
            ->first();

        if (!$user) {
            return redirect()->route('password.request')->with('error', 'This password reset link is invalid or has expired.');
        }

        $user->password      = Hash::make($request->password);
        $user->reset_token   = null;
        $user->reset_expires = null;
        $user->failed_attempts = 0;
        $user->locked_until  = null;
        $user->save();

        return redirect()->route('login')->with('success', 'Your password has been successfully reset! You can now log in.');
    }

    /* ------------------------------------------------------------------
     |  LOGOUT
     | ------------------------------------------------------------------ */

    public function logout(Request $request)
    {
        $role = Auth::check() ? Auth::user()->role : 'customer';

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($role === 'admin') {
            return redirect()->route('admin.login');
        }
        return redirect()->route('login');
    }

    /* ------------------------------------------------------------------
     |  HELPERS
     | ------------------------------------------------------------------ */

    protected function redirectBasedOnRole(User $user)
    {
        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        } elseif ($user->role === 'beautician') {
            return redirect()->route('beautician.dashboard');
        }
        return redirect()->route('customer.dashboard');
    }

    /**
     * Generate a random 6-digit numeric OTP.
     */
    protected function generateOtp(): string
    {
        return str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }

    /**
     * Send the OTP code to the given email address.
     * Returns true on success, false if sending failed.
     */
    protected function sendOtpEmail(string $email, string $otp): bool
    {
        try {
            Mail::send([], [], function ($message) use ($email, $otp) {
                $message->to($email)
                    ->subject('Your BookMyBeauty verification code')
                    ->html("
                        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #eee; border-radius: 8px;'>
                            <h2 style='color: #1a1a2e; margin-bottom: 20px;'>Verify Your Email</h2>
                            <p>Use the code below to complete your BookMyBeauty registration. This code expires in " . self::OTP_EXPIRY_MINUTES . " minutes.</p>
                            <p style='margin: 25px 0; text-align: center;'>
                                <span style='display: inline-block; background: #1a1a2e; color: #c9a96e; padding: 14px 28px; border-radius: 8px; font-size: 28px; font-weight: bold; letter-spacing: 6px;'>{$otp}</span>
                            </p>
                            <p style='color: #777; font-size: 13px;'>If you did not request this code, you can safely ignore this email.</p>
                        </div>
                    ");
            });
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}