<?php

/**
 * Mercatino Libri - Helper Functions
 *
 * Questo file contiene funzioni di utilità globali usate in tutta l'applicazione.
 * Include funzioni per formattazione, calcoli, e operazioni comuni.
 *
 * @author Mercatino Libri Team
 */

/**
 * Formatta un valore numerico come prezzo in EUR
 *
 * @param float $price Il prezzo da formattare
 * @param bool $symbol Se includere il simbolo €
 * @return string Il prezzo formattato (es: "€ 15,50")
 */
if (!function_exists('format_price')) {
    function format_price($price, $symbol = true)
    {
        $formatted = number_format($price, 2, ',', '.');

        return $symbol ? '€ ' . $formatted : $formatted;
    }
}

/**
 * Calcola il prezzo effettivo dopo lo sconto
 *
 * @param float $originalPrice Prezzo originale
 * @param float $discountPercent Percentuale di sconto (0-100)
 * @return float Il prezzo scontato
 */
if (!function_exists('calculate_discount')) {
    function calculate_discount($originalPrice, $discountPercent)
    {
        $discountAmount = ($originalPrice * $discountPercent) / 100;

        return $originalPrice - $discountAmount;
    }
}

/**
 * Calcola la commissione della piattaforma
 *
 * @param float $salePrice Il prezzo di vendita
 * @param float $commissionPercent Percentuale commissione (default 10%)
 * @return array Array con i dati ['commission' => float, 'seller_earnings' => float]
 */
if (!function_exists('calculate_commission')) {
    function calculate_commission($salePrice, $commissionPercent = 10)
    {
        $commission = ($salePrice * $commissionPercent) / 100;
        $sellerEarnings = $salePrice - $commission;

        return [
            'commission' => round($commission, 2),
            'seller_earnings' => round($sellerEarnings, 2),
        ];
    }
}

/**
 * Calcola la percentuale di sconto tra due prezzi
 *
 * @param float $originalPrice Prezzo originale
 * @param float $currentPrice Prezzo attuale
 * @return int Percentuale di sconto (0-100)
 */
if (!function_exists('calculate_discount_percent')) {
    function calculate_discount_percent($originalPrice, $currentPrice)
    {
        if ($originalPrice == 0) {
            return 0;
        }

        $discount = (($originalPrice - $currentPrice) / $originalPrice) * 100;

        return (int) round($discount);
    }
}

/**
 * Converte lo stato della transazione in un label leggibile
 *
 * @param string $status Lo stato della transazione
 * @return string Lo stato formattato
 */
if (!function_exists('format_transaction_status')) {
    function format_transaction_status($status)
    {
        $statusMap = [
            'pending' => 'In Sospeso',
            'paid' => 'Pagato',
            'shipped' => 'Spedito',
            'delivered' => 'Consegnato',
            'completed' => 'Completato',
            'cancelled' => 'Annullato',
            'refunded' => 'Rimborso',
        ];

        return $statusMap[$status] ?? ucfirst($status);
    }
}

/**
 * Restituisce il colore/badge per uno stato di transazione
 *
 * @param string $status Lo stato della transazione
 * @return string La classe CSS per il badge
 */
if (!function_exists('get_status_badge_class')) {
    function get_status_badge_class($status)
    {
        $statusClasses = [
            'pending' => 'badge-warning',
            'paid' => 'badge-success',
            'shipped' => 'badge-info',
            'delivered' => 'badge-success',
            'completed' => 'badge-success',
            'cancelled' => 'badge-error',
            'refunded' => 'badge-error',
        ];

        return $statusClasses[$status] ?? 'badge-primary';
    }
}

/**
 * Converte lo stato del libro in un label leggibile
 *
 * @param string $status Lo stato del libro
 * @return string Lo stato formattato
 */
if (!function_exists('format_book_status')) {
    function format_book_status($status)
    {
        $statusMap = [
            'available' => 'Disponibile',
            'reserved' => 'Riservato',
            'sold' => 'Venduto',
            'archived' => 'Archiviato',
        ];

        return $statusMap[$status] ?? ucfirst($status);
    }
}

/**
 * Converte le condizioni del libro in un label leggibile
 *
 * @param string $condition La condizione del libro
 * @return string La condizione formattata
 */
if (!function_exists('format_book_condition')) {
    function format_book_condition($condition)
    {
        $conditionMap = [
            'like-new' => 'Come Nuovo',
            'good' => 'Buono',
            'fair' => 'Discreto',
            'poor' => 'Consumato',
        ];

        return $conditionMap[$condition] ?? ucfirst($condition);
    }
}

/**
 * Restituisce il colore per una condizione di libro
 *
 * @param string $condition La condizione del libro
 * @return string Il colore (nome classe Tailwind)
 */
if (!function_exists('get_condition_color')) {
    function get_condition_color($condition)
    {
        $colors = [
            'like-new' => 'bg-green-100 text-green-800',
            'good' => 'bg-blue-100 text-blue-800',
            'fair' => 'bg-yellow-100 text-yellow-800',
            'poor' => 'bg-red-100 text-red-800',
        ];

        return $colors[$condition] ?? 'bg-gray-100 text-gray-800';
    }
}

/**
 * Formatta una data nel formato leggibile italiano
 *
 * @param \Carbon\Carbon|string $date La data da formattare
 * @param string $format Il formato della data (short|long|full)
 * @return string La data formattata
 */
if (!function_exists('format_date')) {
    function format_date($date, $format = 'short')
    {
        if (is_string($date)) {
            $date = \Carbon\Carbon::parse($date);
        }

        $formats = [
            'short' => 'd/m/Y',           // 25/12/2023
            'long' => 'd F Y',            // 25 Dicembre 2023
            'full' => 'l, d F Y',         // Martedì, 25 Dicembre 2023
            'time' => 'd/m/Y H:i',        // 25/12/2023 14:30
            'relative' => 'relative',     // "2 giorni fa"
        ];

        if ($format === 'relative') {
            return $date->diffForHumans();
        }

        $dateFormat = $formats[$format] ?? 'd/m/Y';

        // Traduci i nomi dei giorni e mesi in italiano
        $italianMonths = [
            'January' => 'Gennaio',
            'February' => 'Febbraio',
            'March' => 'Marzo',
            'April' => 'Aprile',
            'May' => 'Maggio',
            'June' => 'Giugno',
            'July' => 'Luglio',
            'August' => 'Agosto',
            'September' => 'Settembre',
            'October' => 'Ottobre',
            'November' => 'Novembre',
            'December' => 'Dicembre',
        ];

        $italianDays = [
            'Monday' => 'Lunedì',
            'Tuesday' => 'Martedì',
            'Wednesday' => 'Mercoledì',
            'Thursday' => 'Giovedì',
            'Friday' => 'Venerdì',
            'Saturday' => 'Sabato',
            'Sunday' => 'Domenica',
        ];

        $formatted = $date->format($dateFormat);
        $formatted = str_replace(array_keys($italianMonths), array_values($italianMonths), $formatted);
        $formatted = str_replace(array_keys($italianDays), array_values($italianDays), $formatted);

        return $formatted;
    }
}

/**
 * Tronca un testo a una lunghezza specifica
 *
 * @param string $text Il testo da troncare
 * @param int $length La lunghezza massima
 * @param string $ending Il suffisso (es: "...")
 * @return string Il testo troncato
 */
if (!function_exists('truncate_text')) {
    function truncate_text($text, $length = 100, $ending = '...')
    {
        if (strlen($text) <= $length) {
            return $text;
        }

        return substr($text, 0, $length) . $ending;
    }
}

/**
 * Genera uno slug da un testo
 *
 * @param string $text Il testo da convertire
 * @return string Lo slug (es: "my-book-title")
 */
if (!function_exists('generate_slug')) {
    function generate_slug($text)
    {
        // Converti a minuscolo
        $slug = strtolower($text);

        // Rimuovi accenti
        $slug = transliterate_slug($slug);

        // Sostituisci spazi e caratteri speciali con trattini
        $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);

        // Rimuovi trattini da inizio e fine
        $slug = trim($slug, '-');

        return $slug;
    }
}

/**
 * Rimuove gli accenti da una stringa
 *
 * @param string $text Il testo da pulire
 * @return string Il testo senza accenti
 */
if (!function_exists('transliterate_slug')) {
    function transliterate_slug($text)
    {
        $accents = [
            'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a',
            'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e',
            'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i',
            'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o', 'ö' => 'o',
            'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ü' => 'u',
        ];

        return str_replace(array_keys($accents), array_values($accents), $text);
    }
}

/**
 * Verifica se un email è valido per la registrazione scolastica
 *
 * @param string $email L'email da verificare
 * @param array $allowedDomains Domini email scolastici consentiti
 * @return bool True se l'email è valida per la scuola
 */
if (!function_exists('is_school_email')) {
    function is_school_email($email, $allowedDomains = [])
    {
        if (empty($allowedDomains)) {
            // Default: estensioni comuni per email scolastiche italiane
            $allowedDomains = [
                '@istituto.it',
                '@scuola.it',
                '@liceo.it',
                '@iis.it',
                // Aggiungi altri domini della tua scuola
            ];
        }

        foreach ($allowedDomains as $domain) {
            if (str_ends_with($email, $domain)) {
                return true;
            }
        }

        return false;
    }
}

/**
 * Calcola la reputazione dell'utente basata su voti e transazioni
 *
 * @param int $totalRatings Numero totale di voti ricevuti
 * @param float $averageRating Media dei voti (0-5)
 * @param int $totalSales Numero totale di vendite
 * @return string La reputazione (Nuovo|Affidabile|Molto Affidabile|Eccellente)
 */
if (!function_exists('get_user_reputation')) {
    function get_user_reputation($totalRatings = 0, $averageRating = 0, $totalSales = 0)
    {
        if ($totalSales == 0) {
            return 'Nuovo';
        }

        if ($totalSales < 5) {
            return 'Principiante';
        }

        if ($averageRating < 4 || $totalRatings < 3) {
            return 'Affidabile';
        }

        if ($averageRating >= 4.5 && $totalRatings >= 10) {
            return 'Eccellente';
        }

        return 'Molto Affidabile';
    }
}

/**
 * Formatta la reputazione con il colore appropriato
 *
 * @param string $reputation La reputazione dell'utente
 * @return string La classe CSS per il badge
 */
if (!function_exists('get_reputation_class')) {
    function get_reputation_class($reputation)
    {
        $classes = [
            'Nuovo' => 'badge-primary',
            'Principiante' => 'badge-info',
            'Affidabile' => 'badge-success',
            'Molto Affidabile' => 'badge-success',
            'Eccellente' => 'badge-success',
        ];

        return $classes[$reputation] ?? 'badge-primary';
    }
}

/**
 * Restituisce un messaggio di benvenuto personalizzato per l'ora del giorno
 *
 * @param string $userName Nome dell'utente
 * @return string Il messaggio di benvenuto
 */
if (!function_exists('get_greeting')) {
    function get_greeting($userName = '')
    {
        $hour = date('H');

        if ($hour < 12) {
            $greeting = 'Buongiorno';
        } elseif ($hour < 18) {
            $greeting = 'Buonasera';
        } else {
            $greeting = 'Buonanotte';
        }

        if ($userName) {
            return $greeting . ', ' . $userName . '!';
        }

        return $greeting . '!';
    }
}

/**
 * Formatta il numero di visualizzazioni per un libro
 *
 * @param int $views Numero di visualizzazioni
 * @return string Il numero formattato (es: "1.2K", "50")
 */
if (!function_exists('format_views')) {
    function format_views($views)
    {
        if ($views >= 1000000) {
            return round($views / 1000000, 1) . 'M';
        }

        if ($views >= 1000) {
            return round($views / 1000, 1) . 'K';
        }

        return $views;
    }
}

/**
 * Verifica se due coordinate sono nella stessa area/città
 *
 * @param float $lat1 Latitudine 1
 * @param float $lon1 Longitudine 1
 * @param float $lat2 Latitudine 2
 * @param float $lon2 Longitudine 2
 * @param int $radiusKm Raggio in km (default 5)
 * @return bool True se nella stessa area
 */
if (!function_exists('is_same_area')) {
    function is_same_area($lat1, $lon1, $lat2, $lon2, $radiusKm = 5)
    {
        $distance = calculate_distance($lat1, $lon1, $lat2, $lon2);

        return $distance <= $radiusKm;
    }
}

/**
 * Calcola la distanza tra due coordinate geografiche (formula di Haversine)
 *
 * @param float $lat1 Latitudine 1
 * @param float $lon1 Longitudine 1
 * @param float $lat2 Latitudine 2
 * @param float $lon2 Longitudine 2
 * @return float Distanza in km
 */
if (!function_exists('calculate_distance')) {
    function calculate_distance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371; // Km

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * asin(sqrt($a));

        return $earthRadius * $c;
    }
}
