<?php

namespace Database\Seeders;

use App\Models\Acquisition;
use App\Models\BookDelivery;
use App\Models\BookListing;
use App\Models\User;
use Illuminate\Database\Seeder;

class BookDeliverySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get students
        $marcoRossi = User::where('name', 'Marco')->where('surname', 'Rossi')->first();
        $saraNeri = User::where('name', 'Sara')->where('surname', 'Neri')->first();
        
        // Get a staff member to approve the deliveries
        $staffMember = User::where('role', 'staff')->first();

        if (!$marcoRossi || !$saraNeri) {
            $this->command->error('Students not found. Make sure UserSeeder has been run.');
            return;
        }

        if (!$staffMember) {
            $this->command->error('Staff member not found. Make sure UserSeeder has been run.');
            return;
        }

        // Marco Rossi - Delivery 1
        BookDelivery::create([
            'user_id' => $marcoRossi->id,
            'book_id' => 1, // I Promessi Sposi
            'condition' => 'good',
            'price' => 12.50,
            'status' => 'pending',
            'leave' => false,
        ]);

        BookDelivery::create([
            'user_id' => $marcoRossi->id,
            'book_id' => 2, // Divina Commedia
            'condition' => 'good',
            'price' => 15.00,
            'status' => 'pending',
            'leave' => false,
        ]);

        BookDelivery::create([
            'user_id' => $marcoRossi->id,
            'book_id' => 3, // Il Decameron
            'condition' => 'fair',
            'price' => 10.00,
            'status' => 'pending',
            'leave' => false,
        ]);

        // Marco Rossi - Delivery 2 (APPROVED)
        // Create acquisition for Marco's approved deliveries
        $marcoAcquisition = Acquisition::create([
            'staff_id' => $staffMember->id,
            'seller_id' => $marcoRossi->id,
            'status' => 'completed',
            'total_price' => 28.00 + 32.00 + 38.00, // Sum of prices
            'notes' => 'Consegna libri approvati - Marco Rossi',
        ]);

        // Create book delivery and book listing for each approved delivery
        BookDelivery::create([
            'user_id' => $marcoRossi->id,
            'book_id' => 4, // Matematica Blu
            'condition' => 'like-new',
            'price' => 28.00,
            'status' => 'approved',
            'leave' => false,
        ]);

        BookListing::create([
            'book_id' => 4,
            'seller_id' => $marcoRossi->id,
            'acquisition_id' => $marcoAcquisition->id,
            'condition' => 'like-new',
            'price' => 28.00,
            'status' => 'available',
            'leave' => false,
        ]);

        BookDelivery::create([
            'user_id' => $marcoRossi->id,
            'book_id' => 5, // Fisica
            'condition' => 'good',
            'price' => 32.00,
            'status' => 'approved',
            'leave' => false,
        ]);

        BookListing::create([
            'book_id' => 5,
            'seller_id' => $marcoRossi->id,
            'acquisition_id' => $marcoAcquisition->id,
            'condition' => 'good',
            'price' => 32.00,
            'status' => 'available',
            'leave' => false,
        ]);

        BookDelivery::create([
            'user_id' => $marcoRossi->id,
            'book_id' => 6, // Chimica Organica
            'condition' => 'good',
            'price' => 38.00,
            'status' => 'approved',
            'leave' => false,
        ]);

        BookListing::create([
            'book_id' => 6,
            'seller_id' => $marcoRossi->id,
            'acquisition_id' => $marcoAcquisition->id,
            'condition' => 'good',
            'price' => 38.00,
            'status' => 'available',
            'leave' => false,
        ]);

        // Sara Neri - Delivery 1
        BookDelivery::create([
            'user_id' => $saraNeri->id,
            'book_id' => 7, // Storia
            'condition' => 'good',
            'price' => 22.00,
            'status' => 'pending',
            'leave' => false,
        ]);

        BookDelivery::create([
            'user_id' => $saraNeri->id,
            'book_id' => 8, // New Horizons
            'condition' => 'fair',
            'price' => 18.00,
            'status' => 'pending',
            'leave' => true,
        ]);

        BookDelivery::create([
            'user_id' => $saraNeri->id,
            'book_id' => 9, // Français Écho
            'condition' => 'good',
            'price' => 16.00,
            'status' => 'pending',
            'leave' => false,
        ]);

        // Sara Neri - Delivery 2
        BookDelivery::create([
            'user_id' => $saraNeri->id,
            'book_id' => 10, // Etica Civica
            'condition' => 'like-new',
            'price' => 16.00,
            'status' => 'pending',
            'leave' => false,
        ]);

        BookDelivery::create([
            'user_id' => $saraNeri->id,
            'book_id' => 1, // I Promessi Sposi
            'condition' => 'good',
            'price' => 13.00,
            'status' => 'pending',
            'leave' => false,
        ]);

        BookDelivery::create([
            'user_id' => $saraNeri->id,
            'book_id' => 2, // Divina Commedia
            'condition' => 'like-new',
            'price' => 16.00,
            'status' => 'pending',
            'leave' => false,
        ]);
    }
}
