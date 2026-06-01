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
        Schema::table('book_sale_batches', function (Blueprint $table) {
            // Buyer ID - il primo acquirente/cliente del batch
            $table->foreignId('buyer_id')
                ->nullable()
                ->constrained('users')
                ->onDelete('set null');

            // Total price - prezzo totale del batch
            $table->decimal('total_price', 10, 2)
                ->default(0)
                ->after('notes');

            // Indice per buyer_id
            $table->index('buyer_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('book_sale_batches', function (Blueprint $table) {
            $table->dropForeignIdFor('users', 'buyer_id');
            $table->dropIndex(['buyer_id']);
            $table->dropColumn('buyer_id');
            $table->dropColumn('total_price');
        });
    }
};
