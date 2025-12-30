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
        Schema::create('activities', function (Blueprint $table) {
            $table->id();

            $table->morphs('subject'); // subject_type, subject_id

            $table->foreignId('workspace_id')->constrained('workspaces')->cascadeOnDelete();
            $table->foreignId('owner_id')->nullable()->constrained('users')->nullOnDelete();

            $table->string('type'); // task, call, meeting, email
            $table->string('title');
            $table->text('notes')->nullable();

            $table->dateTime('due_date')->nullable();
            $table->boolean('is_completed')->default(false);

            $table->timestamps();

            $table->index(['workspace_id', 'subject_type', 'subject_id']);
            $table->index(['workspace_id', 'due_date', 'is_completed']);
            $table->index(['workspace_id', 'owner_id', 'is_completed']);
        });


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activities');
    }
};
