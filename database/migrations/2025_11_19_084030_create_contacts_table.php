<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contacts', function (Blueprint $table) {
            $table->id();

            // Ownership / tenancy
            $table->foreignId('account_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('owner_id')->nullable()->constrained('users')->nullOnDelete();

            // Optional link to company
            $table->foreignId('company_id')->nullable()->constrained('companies')->nullOnDelete();

            // Core identity
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->nullable()->index();
            $table->string('phone')->nullable();
            $table->string('mobile')->nullable();
            $table->string('job_title')->nullable();

            // Address
            $table->string('street')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('country')->nullable();

            // CRM info
            $table->string('lifecycle_stage')->default('lead'); // lead, prospect, customer
            $table->string('lead_source')->nullable();
            $table->string('status')->default('active');        // active, inactive, archived

            // Flexible extra data (JSON)
            $table->json('extra')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['account_id', 'tenant_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contacts');
    }
};
