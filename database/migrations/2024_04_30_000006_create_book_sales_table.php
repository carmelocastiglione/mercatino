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
        Schema::create('book_sales', function (Blueprint $table) {
            $table->id();

            // Relazione con il libro listing venduto
            $table->foreignId('book_listing_id')
                ->constrained('book_listings')
                ->onDelete('restrict');

            // Chi ha effettuato la vendita (staff member)
            $table->foreignId('sold_by')
                ->constrained('users')
                ->onDelete('restrict');

            // Note aggiuntive
            $table->text('notes')->nullable()->comment('Note sulla vendita');

            // Timestamp
            $table->timestamps();

            // Indici
            $table->index('book_listing_id');
            $table->index('sold_by');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('book_sales');
    }
};
