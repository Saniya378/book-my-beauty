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
        Schema::table('portfolio_images', function (Blueprint $table) {
            $table->foreign(['beautician_id'], 'portfolio_images_ibfk_1')->references(['id'])->on('beautician_profiles')->onUpdate('restrict')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('portfolio_images', function (Blueprint $table) {
            $table->dropForeign('portfolio_images_ibfk_1');
        });
    }
};
