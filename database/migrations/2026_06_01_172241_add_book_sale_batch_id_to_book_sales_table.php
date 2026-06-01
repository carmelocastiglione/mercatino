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
            $table->foreignId('book_sale_batch_id')
                ->nullable()
                ->after('sold_by')
                ->constrained('book_sale_batches')
                ->onDelete('set null');
            
            $table->index('book_sale_batch_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('book_sales', function (Blueprint $table) {
            $table->dropForeignKeyIfExists(['book_sale_batch_id']);
            $table->dropColumn('book_sale_batch_id');
        });
    }
};
