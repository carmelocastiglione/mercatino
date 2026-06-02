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
        Schema::table('book_listings', function (Blueprint $table) {
            // Prezzo di vendita (può differire dal prezzo di acquisizione)
            $table->decimal('price_sell', 8, 2)->nullable()->after('price')->comment('Prezzo di vendita');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('book_listings', function (Blueprint $table) {
            $table->dropColumn('price_sell');
        });
    }
};
