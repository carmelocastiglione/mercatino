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
        Schema::table('book_sales', function (Blueprint $table) {
            $table->foreignId('reclaim_id')
                  ->nullable()
                  ->constrained('reclaims')
                  ->cascadeOnDelete();
            $table->timestamp('reclaimed_at')
                  ->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('book_sales', function (Blueprint $table) {
            $table->dropForeignKeyIfExists(['reclaim_id']);
            $table->dropColumn(['reclaim_id', 'reclaimed_at']);
        });
    }
};
