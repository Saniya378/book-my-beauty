<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('name', 100);
            $table->string('email', 100)->unique('email');
            $table->string('phone')->nullable();
            $table->string('password');
            $table->enum('role', ['customer', 'beautician', 'admin']);
            $table->timestamp('created_at')->useCurrent();
            $table->string('otp_code', 6)->nullable();
            $table->dateTime('otp_expires')->nullable();
            $table->integer('failed_attempts')->default(0);
            $table->dateTime('locked_until')->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('reset_token', 64)->nullable();
            $table->dateTime('reset_expires')->nullable();
            $table->rememberToken();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
