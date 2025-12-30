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
        Schema::create('workspaces', function (Blueprint $table) {
            $table->id();

            $table->foreignId('account_id')->constrained('accounts')->cascadeOnDelete();

            $table->string('name');
            $table->string('slug')->unique();

            $table->string('status')->default('active'); // active, suspended
            $table->string('industry_key')->nullable();  // roofing, personal, auto_repair, etc.
            $table->string('timezone')->default('Asia/Kolkata');
            $table->string('currency', 3)->default('USD');

            $table->foreignId('owner_user_id')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['account_id', 'status']);
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workspaces');
    }
};
