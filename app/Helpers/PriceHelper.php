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
}
