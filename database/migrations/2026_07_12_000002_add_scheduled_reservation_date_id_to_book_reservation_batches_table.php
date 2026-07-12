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
        Schema::table('book_reservation_batches', function (Blueprint $table) {
            $table->foreignId('scheduled_reservation_date_id')
                ->nullable()
                ->constrained('school_reservation_dates')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('book_reservation_batches', function (Blueprint $table) {
            $table->dropForeignIdFor(\App\Models\SchoolReservationDate::class, 'scheduled_reservation_date_id');
        });
    }
};
