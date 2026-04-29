<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * LoginController
 *
 * Gestisce l'autenticazione degli utenti tramite email e password.
 * Fornisce la visualizzazione del form di login e processa le credenziali.
 */
class LoginController extends Controller
{
    /**
     * Visualizza il form di login
     *
     * @return \Illuminate\View\View Il form di login
     */
    public function show()
    {
        return view('auth.login', [
            'title' => 'Accedi al tuo account - Mercatino Libri',
            'description' => 'Accedi al tuo account per comprare e vendere libri scolastici usati',
        ]);
    }

    /**
     * Processa il tentativo di login
     *
     * @param Request $request I dati del form di login
     * @return \Illuminate\Http\Response Reindirizza all'home o al form se fallisce
     */
    public function store(Request $request)
    {
        // Validazione dei dati
        $credentials = $request->validate([
            'email' => 'required|email:rfc,dns',
            'password' => 'required|min:6',
            'remember' => 'nullable|boolean',
        ], [
            'email.required' => 'L\'email è obbligatoria',
            'email.email' => 'Inserisci un\'email valida',
            'password.required' => 'La password è obbligatoria',
            'password.min' => 'La password deve contenere almeno 6 caratteri',
        ]);

        // Tentativo di autenticazione
        if (Auth::attempt(
            ['email' => $credentials['email'], 'password' => $credentials['password']],
            $request->has('remember')
        )) {
            // Autenticazione riuscita
            $request->session()->regenerate();
            return redirect()->intended('/dashboard')->with('success', 'Benvenuto nel tuo account!');
        }

        // Autenticazione fallita
        return back()->withErrors([
            'email' => 'Le credenziali fornite non sono valide.',
        ])->onlyInput('email');
    }

    /**
     * Logout dell'utente
     *
     * @param Request $request La richiesta HTTP
     * @return \Illuminate\Http\RedirectResponse Reindirizza all'home
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/')->with('success', 'Logout eseguito con successo');
    }
}
