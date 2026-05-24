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
        Schema::table('book_deliveries', function (Blueprint $table) {
            $table->foreignId('batch_id')->nullable()->after('id')->constrained('book_delivery_batches')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('book_deliveries', function (Blueprint $table) {
            $table->dropForeignKeyConstraints();
            $table->dropColumn('batch_id');
        });
    }
};
