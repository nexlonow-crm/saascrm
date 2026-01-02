<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stages', function (Blueprint $table) {
            // ✅ 1) Ensure there is an index starting with pipeline_id (FK needs it)
            // (This is safe even if you already have other composite indexes.)
            $table->index('pipeline_id', 'stages_pipeline_id_idx');
        });

        Schema::table('stages', function (Blueprint $table) {
            // ✅ 2) Drop FK first (so MySQL allows dropping the unique index)
            $table->dropForeign(['pipeline_id']);

            // ✅ 3) Drop the UNIQUE index (pipeline_id, position)
            $table->dropUnique('stages_pipeline_id_position_unique');

            // ✅ 4) Re-add FK (now it will use stages_pipeline_id_idx)
            $table->foreign('pipeline_id')
                ->references('id')
                ->on('pipelines')
                ->cascadeOnDelete();

            // ✅ 5) Add your new columns (if not added yet)
            if (!Schema::hasColumn('stages', 'label')) {
                $table->string('label')->nullable()->after('name');
            }
            if (!Schema::hasColumn('stages', 'badge_color')) {
                $table->string('badge_color', 20)->nullable()->after('label');
            }

            // ✅ 6) Soft deletes (optional but recommended)
            if (!Schema::hasColumn('stages', 'deleted_at')) {
                $table->softDeletes();
            }
        });
    }

    public function down(): void
    {
        Schema::table('stages', function (Blueprint $table) {
            // Drop FK so we can restore unique
            $table->dropForeign(['pipeline_id']);

            // Remove added columns
            if (Schema::hasColumn('stages', 'deleted_at')) {
                $table->dropSoftDeletes();
            }
            if (Schema::hasColumn('stages', 'badge_color')) {
                $table->dropColumn('badge_color');
            }
            if (Schema::hasColumn('stages', 'label')) {
                $table->dropColumn('label');
            }

            // Restore UNIQUE index
            $table->unique(['pipeline_id', 'position'], 'stages_pipeline_id_position_unique');

            // Re-add FK (it can use the unique index again)
            $table->foreign('pipeline_id')
                ->references('id')
                ->on('pipelines')
                ->cascadeOnDelete();
        });

        Schema::table('stages', function (Blueprint $table) {
            // Drop helper index
            $table->dropIndex('stages_pipeline_id_idx');
        });
    }
};
