<?php

namespace App\Helpers;

use App\Models\Acquisition;

class EAN13Helper
{
    /**
     * Generate a unique EAN13 code (12 random digits + 1 check digit).
     */
    public static function generate(): string
    {
        do {
            $base = str_pad(rand(0, 999999999999), 12, '0', STR_PAD_LEFT);
            $checkDigit = self::calculateCheckDigit($base);
            $ean13 = $base . $checkDigit;
        } while (Acquisition::where('ean13', $ean13)->exists());

        return $ean13;
    }

    /**
     * Calculate the EAN13 check digit.
     * Algorithm: sum of (position_odd * 1 + position_even * 3), then check digit = (10 - (sum % 10)) % 10
     */
    public static function calculateCheckDigit(string $base): int
    {
        $sum = 0;
        for ($i = 0; $i < 12; $i++) {
            $digit = (int)$base[$i];
            // Posizioni pari (0-based) moltiplicati per 1, posizioni dispari per 3
            $sum += ($i % 2 === 0) ? $digit : $digit * 3;
        }
        return (10 - ($sum % 10)) % 10;
    }

    /**
     * Validate an EAN13 code.
     */
    public static function validate(string $ean13): bool
    {
        if (strlen($ean13) !== 13 || !ctype_digit($ean13)) {
            return false;
        }

        $base = substr($ean13, 0, 12);
        $providedCheckDigit = (int)$ean13[12];
        $calculatedCheckDigit = self::calculateCheckDigit($base);

        return $providedCheckDigit === $calculatedCheckDigit;
    }
}
