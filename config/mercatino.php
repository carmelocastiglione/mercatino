<?php

/**
 * Configurazione del Mercatino Libri
 *
 * File di configurazione personalizzato per le impostazioni specifiche
 * del mercatino di libri usati della scuola.
 *
 * Accedi via: config('mercatino.*')
 * Esempio: config('mercatino.commission_percent')
 */

return [

    /**
     * ===== Impostazioni Commissioni =====
     */

    // Percentuale commissione sulla vendita (default 10%)
    'commission_percent' => env('MERCATINO_COMMISSION_PERCENT', 10),

    // Importo minimo commissione in EUR
    'min_commission' => 0.50,

    // Importo massimo commissione in EUR (0 = no limit)
    'max_commission' => 0,

    /**
     * ===== Categorie e Materie =====
     */

    'subjects' => [
        'matematica' => 'Matematica',
        'italiano' => 'Italiano',
        'inglese' => 'Inglese',
        'storia' => 'Storia',
        'geografia' => 'Geografia',
        'scienze' => 'Scienze',
        'biologia' => 'Biologia',
        'chimica' => 'Chimica',
        'fisica' => 'Fisica',
        'latino' => 'Latino',
        'greco' => 'Greco',
        'arte' => 'Arte e Immagine',
        'musica' => 'Musica',
        'educazione_fisica' => 'Educazione Fisica',
        'informatica' => 'Informatica',
        'economia' => 'Economia',
        'diritto' => 'Diritto',
        'filosofia' => 'Filosofia',
        'religione' => 'Religione',
        'letteratura' => 'Letteratura',
        'altro' => 'Altro',
    ],

    /**
     * ===== Classi Scolastiche =====
     */

    'school_classes' => [
        '1ª' => 'Primo Anno',
        '2ª' => 'Secondo Anno',
        '3ª' => 'Terzo Anno',
        '4ª' => 'Quarto Anno',
        '5ª' => 'Quinto Anno',
    ],

    /**
     * ===== Condizioni Libri =====
     */

    'book_conditions' => [
        'like-new' => [
            'label' => 'Come Nuovo',
            'description' => 'Praticamente non utilizzato, condizioni perfette',
            'discount_percent' => 10,
        ],
        'good' => [
            'label' => 'Buono',
            'description' => 'Pochi segni di utilizzo, facilmente leggibile',
            'discount_percent' => 30,
        ],
        'fair' => [
            'label' => 'Discreto',
            'description' => 'Visibili segni di utilizzo, ma completamente leggibile',
            'discount_percent' => 45,
        ],
        'poor' => [
            'label' => 'Consumato',
            'description' => 'Molto utilizzato, può avere evidenziature o sottolineature',
            'discount_percent' => 60,
        ],
    ],

    /**
     * ===== Metodi di Pagamento =====
     */

    'payment_methods' => [
        'paypal' => 'PayPal',
        'stripe' => 'Carta di Credito (Stripe)',
        'credit_card' => 'Carta di Credito Diretta',
        'bank_transfer' => 'Bonifico Bancario',
        'satispay' => 'Satispay',
        'cash' => 'Contanti (Incontro in persona)',
    ],

    /**
     * ===== Metodi di Consegna =====
     */

    'delivery_methods' => [
        'school_meeting' => [
            'label' => 'Incontro a Scuola',
            'cost' => 0,
            'description' => 'Incontro gratuito nell\'area della scuola',
            'delivery_days' => 1,
        ],
        'postal' => [
            'label' => 'Posta Italiana',
            'cost' => 3.50,
            'description' => 'Spedizione tramite Poste Italiane',
            'delivery_days' => 3,
        ],
        'pickup_point' => [
            'label' => 'Ritiro in Negozio',
            'cost' => 0,
            'description' => 'Ritira presso i nostri punti di raccolta',
            'delivery_days' => 1,
        ],
        'courier' => [
            'label' => 'Corriere Espresso',
            'cost' => 8.50,
            'description' => 'Consegna veloce (24-48 ore)',
            'delivery_days' => 2,
        ],
    ],

    /**
     * ===== Status Transazioni =====
     */

    'transaction_statuses' => [
        'pending' => 'In Sospeso',
        'paid' => 'Pagato',
        'shipped' => 'Spedito',
        'delivered' => 'Consegnato',
        'completed' => 'Completato',
        'cancelled' => 'Annullato',
        'refunded' => 'Rimborso',
    ],

    /**
     * ===== Status Libri =====
     */

    'book_statuses' => [
        'available' => 'Disponibile',
        'reserved' => 'Riservato',
        'sold' => 'Venduto',
        'archived' => 'Archiviato',
    ],

    /**
     * ===== Limiti e Restrizioni =====
     */

    // Prezzo minimo e massimo per un libro
    'price_min' => 1.00,
    'price_max' => 500.00,

    // Numero massimo di foto per libro
    'max_images_per_book' => 5,

    // Dimensione massima foto in MB
    'max_image_size_mb' => 2,

    // Numero massimo di libri per utente non verificato
    'max_listings_unverified' => 3,

    // Numero massimo di libri per utente verificato
    'max_listings_verified' => 50,

    // Giorni di validità di un annuncio
    'listing_validity_days' => 90,

    /**
     * ===== Impostazioni Reputazione =====
     */

    // Rating minimo per essere considerato affidabile
    'trusted_rating_min' => 4.0,

    // Numero minimo di voti per essere considerato affidabile
    'trusted_votes_min' => 5,

    /**
     * ===== Email e Notifiche =====
     */

    // Email da cui vengono inviate le notifiche
    'notification_email' => env('MERCATINO_EMAIL', 'noreply@mercatinolibri.it'),

    // Nome dell'azienda/scuola
    'school_name' => env('MERCATINO_SCHOOL_NAME', 'Mercatino Libri'),

    /**
     * ===== Domini Email Scolastici Autorizzati =====
     */

    'allowed_email_domains' => explode(',', env('MERCATINO_ALLOWED_DOMAINS', '@istituto.it,@scuola.it,@liceo.it')),

    /**
     * ===== SEO =====
     */

    'seo' => [
        'title' => 'Mercatino Libri Scolastici - Compra e Vendi Libri Usati',
        'description' => 'Piattaforma per comprare e vendere libri scolastici usati. Risparmia fino al 50% sui tuoi libri di testo.',
        'keywords' => 'libri usati, libri scolastici, mercatino, scuola, usato, sconti',
    ],

    /**
     * ===== Colori Tema =====
     */

    'theme' => [
        'primary' => '#667eea',
        'secondary' => '#764ba2',
        'accent' => '#ff6b35',
        'success' => '#10b981',
        'warning' => '#f59e0b',
        'error' => '#ef4444',
    ],

];
