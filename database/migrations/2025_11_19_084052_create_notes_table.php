<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {  
        Schema::create('notes', function (Blueprint $table) {
            $table->id();

            $table->morphs('subject'); // subject_type, subject_id

            $table->foreignId('workspace_id')
                ->constrained('workspaces')
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->text('body');
            $table->boolean('is_pinned')->default(false);

            $table->timestamps();
            $table->softDeletes();

            $table->index(['workspace_id', 'subject_type', 'subject_id']);
            $table->index(['workspace_id', 'user_id']);
        });


    }

    public function down(): void
    {
        Schema::dropIfExists('notes');
    }
};
