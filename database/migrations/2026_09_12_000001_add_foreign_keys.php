<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // beautician_profiles.user_id -> users.id
        Schema::table('beautician_profiles', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        // services.beautician_id -> beautician_profiles.id
        Schema::table('services', function (Blueprint $table) {
            $table->foreign('beautician_id')->references('id')->on('beautician_profiles')->onDelete('cascade');
        });

        // availability.beautician_id -> beautician_profiles.id
        Schema::table('availability', function (Blueprint $table) {
            $table->foreign('beautician_id')->references('id')->on('beautician_profiles')->onDelete('cascade');
        });

        // portfolio_images.beautician_id -> beautician_profiles.id
        Schema::table('portfolio_images', function (Blueprint $table) {
            $table->foreign('beautician_id')->references('id')->on('beautician_profiles')->onDelete('cascade');
        });

        // reviews.customer_id -> users.id, reviews.beautician_id -> beautician_profiles.id
        Schema::table('reviews', function (Blueprint $table) {
            $table->foreign('customer_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('beautician_id')->references('id')->on('beautician_profiles')->onDelete('cascade');
        });

        // favorites.customer_id -> users.id, favorites.beautician_id -> beautician_profiles.id
        Schema::table('favorites', function (Blueprint $table) {
            $table->foreign('customer_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('beautician_id')->references('id')->on('beautician_profiles')->onDelete('cascade');
        });

        // notifications.user_id -> users.id
        Schema::table('notifications', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        // bookings.customer_id -> users.id, bookings.service_id -> services.id
        Schema::table('bookings', function (Blueprint $table) {
            $table->foreign('customer_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('service_id')->references('id')->on('services')->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropForeign(['customer_id']);
            $table->dropForeign(['service_id']);
        });

        Schema::table('notifications', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });

        Schema::table('favorites', function (Blueprint $table) {
            $table->dropForeign(['customer_id']);
            $table->dropForeign(['beautician_id']);
        });

        Schema::table('reviews', function (Blueprint $table) {
            $table->dropForeign(['customer_id']);
            $table->dropForeign(['beautician_id']);
        });

        Schema::table('portfolio_images', function (Blueprint $table) {
            $table->dropForeign(['beautician_id']);
        });

        Schema::table('availability', function (Blueprint $table) {
            $table->dropForeign(['beautician_id']);
        });

        Schema::table('services', function (Blueprint $table) {
            $table->dropForeign(['beautician_id']);
        });

        Schema::table('beautician_profiles', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });
    }
};
