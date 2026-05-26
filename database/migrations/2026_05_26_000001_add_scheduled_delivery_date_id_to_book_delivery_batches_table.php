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
        Schema::table('book_delivery_batches', function (Blueprint $table) {
            $table->foreignId('scheduled_delivery_date_id')
                ->nullable()
                ->after('school_id')
                ->constrained('school_delivery_dates')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('book_delivery_batches', function (Blueprint $table) {
            $table->dropForeignKeyConstraints();
            $table->dropColumn('scheduled_delivery_date_id');
        });
    }
};
