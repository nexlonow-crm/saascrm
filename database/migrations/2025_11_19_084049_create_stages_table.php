<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stages', function (Blueprint $table) {
            $table->id();

            $table->foreignId('workspace_id')->constrained('workspaces')->cascadeOnDelete();
            $table->foreignId('pipeline_id')->constrained('pipelines')->cascadeOnDelete();

            $table->string('name');
            $table->unsignedTinyInteger('probability')->nullable();
            $table->unsignedInteger('position')->default(0);

            $table->timestamps();

            $table->index(['workspace_id', 'pipeline_id', 'position']);
            $table->unique(['pipeline_id', 'position']);
        });

    }

    public function down(): void
    {
        Schema::dropIfExists('stages');
    }
};
