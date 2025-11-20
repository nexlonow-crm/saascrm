<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();

            // Core
            $table->string('name');            // Account / workspace name
            $table->string('plan')->nullable(); // e.g. free, pro, enterprise

            // Simple lifecycle fields (you can extend later)
            $table->boolean('is_active')->default(true);
            $table->timestamp('trial_ends_at')->nullable();

            // Audit
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounts');
    }
};
