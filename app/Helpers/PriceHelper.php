<?php

namespace App\Helpers;

use App\Models\School;

class PriceHelper
{
    /**
     * Calcola il prezzo di un libro con la fee della scuola applicata
     * 
     * @param float $originalPrice Prezzo originale del libro
     * @param School $school Scuola per ottenere la fee
     * @param bool $isSale true = vendita (sottrae fee), false = acquisto (aggiunge fee)
     * @return array Array con original_price, marketplace_price, fee, total
     */

    // Questa funzione viene utilizzata solamente per calcolare il prezzo di ACQUISIZIONE, perché lo staff può modificare il prezzo in fase di acquisizione quindi porterebbe ad errori calcolare il prezzo di vendita qui. Rimane il calcolo del prezzo di vendita per completezza ma questa funzione deve essere rivista in futuro per gestire meglio i casi di vendita con prezzo modificato dallo staff.
    public static function calculatePrice($originalPrice, School $school, $isSale = true)
    {
        // Metà del prezzo, arrotondato per difetto
        // Formula: floor(originalPrice) / 2
        $marketplacePrice = floor($originalPrice) / 2;
        
        // Ottieni la fee appropriata
        $fee = $isSale ? $school->sales_fee : $school->purchase_fee;
        
        // Calcola il totale: sottrae se vendita, aggiunge se acquisto
        $total = $isSale 
            ? $marketplacePrice - $fee  
            : $marketplacePrice + $fee;

        return [
            'original_price' => (float) $originalPrice,
            'marketplace_price' => (float) $marketplacePrice,
            'fee' => (float) $fee,
            'total' => (float) $total,
            'is_sale' => $isSale,
        ];
    }

    /**
     * Calcola il prezzo di vendita di un libro al momento dell'acquisizione
     * 
     * @param float $acquisitionPrice Prezzo di acquisizione del libro
     * @param School $school Scuola per ottenere le fee
     * @return float Prezzo di vendita = acquisitionPrice + purchase_fee + sales_fee
     */
    public static function calculateSellingPrice($acquisitionPrice, School $school)
    {
        $purchaseFee = $school->purchase_fee ?? 0;
        $salesFee = $school->sales_fee ?? 0;
        
        return (float) ($acquisitionPrice + $purchaseFee + $salesFee);
    }
}