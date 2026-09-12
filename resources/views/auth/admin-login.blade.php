<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - BookMyBeauty</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Poppins:wght@300;400;500;600&display=swap"
        rel="stylesheet">
    <style>
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            background: #0d0f1a;
            margin: 0;
            font-family: 'Poppins', sans-serif;
            padding: 20px;
        }

        .admin-card {
            background: #1a1a2e;
            width: 100%;
            max-width: 420px;
            padding: 40px;
            border-radius: 16px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.5);
            border: 1px solid rgba(201, 169, 110, 0.3);
            color: white;
        }

        .admin-card .logo {
            text-align: center;
            margin-bottom: 25px;
        }

        .admin-card h2 {
            font-family: 'Playfair Display', serif;
            font-size: 24px;
            color: white;
            margin-bottom: 6px;
        }

        .admin-card h2 span {
            color: var(--accent);
        }

        .admin-badge {
            background: var(--accent);
            color: #1a1a2e;
            font-size: 10px;
            font-weight: 800;
            padding: 3px 10px;
            border-radius: 4px;
            text-transform: uppercase;
            letter-spacing: 1px;
            display: inline-block;
            margin-top: 5px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-size: 13px;
            color: rgba(255, 255, 255, 0.8);
            margin-bottom: 6px;
        }

        .form-group input {
            width: 100%;
            padding: 12px 14px;
            background: #111424;
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 8px;
            color: white;
            font-family: inherit;
            font-size: 14px;
            outline: none;
            transition: border-color 0.2s;
        }

        .form-group input:focus {
            border-color: var(--accent);
        }
    </style>
</head>

<body>
    <div class="admin-card">
        <div class="logo">
            <h2>Beauty <span>Portal</span></h2>
            <span class="admin-badge">Admin Authentication</span>
        </div>

        @include('components.alert')

        <form action="{{ route('admin.login.submit') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="email">Administrator Email</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus
                    placeholder="admin@beauty.com">
            </div>

            <div class="form-group">
                <label for="password">Administrator Password</label>
                <input type="password" id="password" name="password" required placeholder="••••••••">
            </div>

            <button type="submit" class="btn"
                style="width: 100%; padding: 12px; font-size: 14px; border-radius: 8px; background: var(--accent); color: #1a1a2e; font-weight: 700;">Enter
                Console</button>
        </form>

        <div style="margin-top: 25px; text-align: center; font-size: 12px; color: rgba(255,255,255,0.4);">
            Restricted area. All access attempts are logged and monitored.
        </div>
    </div>
</body>

</html>
