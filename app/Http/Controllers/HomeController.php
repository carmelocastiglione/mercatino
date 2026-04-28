<?php

namespace App\Http\Controllers;

/**
 * HomeController
 *
 * Gestisce la visualizzazione della home page del mercatino di libri usati.
 * Questa classe è responsabile del rendering della pagina principale del sito
 * con tutte le informazioni e le call-to-action per gli utenti.
 */
class HomeController extends Controller
{
    /**
     * Visualizza la home page del mercatino
     *
     * @return \Illuminate\View\View La view della home page con SEO metadata
     */
    public function index()
    {
        // Dati per il SEO della pagina home
        $seoData = [
            'title' => 'Mercatino Libri Scolastici - Compra e Vendi Libri Usati',
            'description' => 'Piattaforma per comprare e vendere libri scolastici usati. Risparmia fino al 50% sui tuoi libri di testo. Accedi now per iniziare!',
            'keywords' => 'libri usati, libri scolastici, mercatino, usato, sconti, compra libri, vendi libri',
        ];

        return view('home', $seoData);
    }
}
