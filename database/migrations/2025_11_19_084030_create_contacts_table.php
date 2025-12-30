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

        $table->foreignId('workspace_id')->constrained('workspaces')->cascadeOnDelete();
        $table->foreignId('owner_id')->nullable()->constrained('users')->nullOnDelete();

        $table->foreignId('company_id')->nullable()->constrained('companies')->nullOnDelete();

        $table->string('first_name');
        $table->string('last_name');
        $table->string('email')->nullable();
        $table->string('phone')->nullable();
        $table->string('mobile')->nullable();
        $table->string('job_title')->nullable();

        $table->string('street')->nullable();
        $table->string('city')->nullable();
        $table->string('state')->nullable();
        $table->string('postal_code')->nullable();
        $table->string('country')->nullable();

        $table->string('lifecycle_stage')->default('lead'); // lead, prospect, customer
        $table->string('lead_source')->nullable();
        $table->string('status')->default('active');        // active, inactive, archived

        $table->json('extra')->nullable();

        $table->timestamps();
        $table->softDeletes();

        $table->index(['workspace_id', 'last_name', 'first_name']);
        $table->index(['workspace_id', 'email']);
        $table->index(['workspace_id', 'company_id']);
        $table->index(['workspace_id', 'owner_id']);
    });


    }

    public function down(): void
    {
        Schema::dropIfExists('contacts');
    }
};
