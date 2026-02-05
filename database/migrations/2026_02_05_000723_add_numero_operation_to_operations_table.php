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
            $table->string('numero_operation')->after('id');
            $table->index('numero_operation');
            $table->index(['journal_id', 'numero_operation']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('operations', function (Blueprint $table) {
            $table->dropIndex(['journal_id', 'numero_operation']);
            $table->dropIndex(['numero_operation']);
            $table->dropColumn('numero_operation');
        });
    }
};
