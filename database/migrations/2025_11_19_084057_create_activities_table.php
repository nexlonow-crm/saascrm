<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activities', function (Blueprint $table) {
            $table->id();

            // Ownership / tenancy
            $table->foreignId('account_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete(); // who created / owns activity

            // Links to CRM records (all optional)
            $table->foreignId('contact_id')->nullable()->constrained('contacts')->nullOnDelete();
            $table->foreignId('company_id')->nullable()->constrained('companies')->nullOnDelete();
            $table->foreignId('deal_id')->nullable()->constrained('deals')->nullOnDelete();

            // Core fields
            $table->string('subject')->nullable();  // short title
            $table->string('type')->default('note'); // note, call, meeting, task, email, other
            $table->text('body')->nullable();

            // Scheduling
            $table->timestamp('due_at')->nullable();  // for tasks/meetings
            $table->timestamp('done_at')->nullable(); // when completed

            // Flexible extra data (JSON)
            $table->json('extra')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['account_id', 'tenant_id']);
            $table->index(['type', 'due_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activities');
    }
};
