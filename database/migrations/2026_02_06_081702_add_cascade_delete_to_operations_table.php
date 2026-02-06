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
        Schema::table('operations', function (Blueprint $table) {
            // Drop existing foreign key
            $table->dropForeign(['journal_id']);
            
            // Add foreign key with cascade delete
            $table->foreign('journal_id')
                  ->references('id')
                  ->on('journals')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('operations', function (Blueprint $table) {
            // Drop cascade foreign key
            $table->dropForeign(['journal_id']);
            
            // Restore original foreign key
            $table->foreign('journal_id')
                  ->references('id')
                  ->on('journals');
        });
    }
};
