<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class GoogleController extends Controller
{
    /**
     * Redirect to Google OAuth
     */
    public function redirect()
    {
        return Socialite::driver('google')
            ->stateless()
            ->redirect();
    }

    /**
     * Handle Google OAuth callback
     * Only allows login if email exists in database
     */
    public function callback()
    {
        try {
            // Get Google user - with SSL verification disabled for local development
            $googleUser = Socialite::driver('google')
                ->stateless()
                ->setHttpClient(
                    new \GuzzleHttp\Client([
                        'verify' => app()->environment('production'), // Disable SSL verification in local/staging
                    ])
                )
                ->user();
            
            // Search for user by email (strict matching)
            $user = User::where('email', $googleUser->email)->first();
            
            if (!$user) {
                Log::warning('Google SSO login attempt for non-existent email', [
                    'email' => $googleUser->email,
                    'google_id' => $googleUser->id,
                ]);
                return redirect('/login')->with('error', 'Email non trovata nel sistema. Contatta l\'amministratore.');
            }

            // Update Google tokens
            $user->update([
                'google_id' => $googleUser->id,
                'google_token' => $googleUser->token,
                'google_refresh_token' => $googleUser->refreshToken,
            ]);

            Auth::login($user, remember: true);
            
            Log::info('User logged in via Google SSO', [
                'user_id' => $user->id,
                'email' => $user->email,
            ]);
            
            $user = Auth::user();
            $redirect = match($user->role) {
                'admin' => '/admin',
                'studente' => '/student',
                'staff' => '/staff',
                default => '/dashboard',
            };
            
            return redirect($redirect);
        } catch (\Exception $e) {
            Log::error('Google SSO authentication error', [
                'error' => $e->getMessage(),
            ]);
            return redirect('/login')->with('error', 'Autenticazione Google fallita. Riprova più tardi.');
        }
    }
}
