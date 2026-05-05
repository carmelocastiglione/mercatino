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
            $table->foreignId('acquisition_id')->nullable()->after('seller_id')->constrained('acquisitions')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('book_listings', function (Blueprint $table) {
            $table->dropForeignKeyIfExists(['acquisition_id']);
            $table->dropColumn('acquisition_id');
        });
    }
};
