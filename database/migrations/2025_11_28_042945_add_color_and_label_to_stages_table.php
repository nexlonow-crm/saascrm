<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('stages', function (Blueprint $table) {
            // short label like "Cold", "Warm", "Hot"
            $table->string('label')->nullable()->after('name');

            // bootstrap badge color: primary, secondary, success, warning, danger, info, dark
            $table->string('badge_color')->nullable()->after('label');
        });
    }

    public function down(): void
    {
        Schema::table('stages', function (Blueprint $table) {
            $table->dropColumn(['label', 'badge_color']);
        });
    }
};
