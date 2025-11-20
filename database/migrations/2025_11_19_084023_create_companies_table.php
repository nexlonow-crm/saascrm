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
        Schema::create('companies', function (Blueprint $table) {
            // Primary key
            $table->id();

            // Foreign keys / ownership
            $table->foreignId('account_id')
                ->constrained('accounts')           // assumes "accounts" table
                ->cascadeOnDelete();

            $table->foreignId('tenant_id')
                ->nullable()
                ->constrained('tenants')            // assumes "tenants" table
                ->cascadeOnDelete();

            // Owner (account manager)
            $table->foreignId('owner_id')
                ->nullable()
                ->constrained('users')              // assumes "users" table for account managers
                ->nullOnDelete();

            // Core
            $table->string('name');
            $table->string('domain')->nullable();   // e.g. "example.com"
            $table->string('website')->nullable();
            $table->string('phone')->nullable();
            $table->string('industry')->nullable();
            $table->string('size')->nullable();     // e.g. "1-10", "11-50"

            // Address
            $table->string('street')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('country')->nullable();

            // Extra JSON
            $table->json('extra')->nullable();

            // Audit
            $table->timestamps();
            $table->softDeletes();

            // Optional useful indexes
            $table->index('domain');
            $table->index('tenant_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
