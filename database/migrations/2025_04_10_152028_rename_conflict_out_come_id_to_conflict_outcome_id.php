<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // This migration is now a no-op since the column is already named correctly
        // in the original migration file
        
        // Check if the table has the old column name
        $hasOldColumn = Schema::hasColumn('wildlife_conflict_outcomes', 'conflict_out_come_id');
        
        if ($hasOldColumn) {
            // Only rename if the old column exists
            DB::statement('ALTER TABLE wildlife_conflict_outcomes RENAME COLUMN conflict_out_come_id TO conflict_outcome_id');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This migration is a no-op, so the down method is also a no-op
        // We don't want to rename conflict_outcome_id back to conflict_out_come_id
        // since that was never the intended column name
    }
};
