<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

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
            'email' => 'required|email:rfc',
            'password' => 'required|min:6',
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
            
            $user = Auth::user();

            // Controlli solo per gli studenti
            if ($user->role === 'studente' && $user->school) {
                $school = $user->school;
                
                // Controlla se la scuola ha abilitate le vendite online
                if (!$school->hasFeatureEnabled('enable_online_sales')) {
                    Auth::logout();
                    return back()->withErrors([
                        'email' => 'Le vendite online non sono abilitate per la tua scuola.',
                    ])->onlyInput('email');
                }

                // Controlla se la data odierna è prima della data di apertura negozio online
                $onlineOpeningDate = $school->getSetting('online_opening_date');
                if ($onlineOpeningDate) {
                    $openingDateTime = Carbon::createFromFormat('Y-m-d\TH:i', $onlineOpeningDate);
                    if (Carbon::now() < $openingDateTime) {
                        Auth::logout();
                        $formattedDate = $openingDateTime->format('d/m/Y H:i');
                        return back()->withErrors([
                            'email' => "Il negozio online aprirà il {$formattedDate}. Torna più tardi.",
                        ])->onlyInput('email');
                    }
                }
            }
            
            $redirect = match($user->role) {
                'admin' => '/admin',
                'studente' => '/student',
                'staff' => '/staff',
                default => '/dashboard',
            };
            
            return redirect()->intended($redirect)->with('success', 'Benvenuto nel tuo account!');
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
