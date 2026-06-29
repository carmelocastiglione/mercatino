<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingsController extends Controller
{
    /**
     * Show general settings
     */
    public function general(): View
    {
        $school = auth()->user()->school;
        
        return view('staff.settings.general', [
            'school' => $school,
        ]);
    }

    /**
     * Save settings
     */
    public function save(Request $request)
    {
        $school = auth()->user()->school;
        
        if ($request->has('enable_online_sales')) {
            $school->setSetting('enable_online_sales', $request->input('enable_online_sales'));
        }
        
        if ($request->has('referring_name')) {
            $school->setSetting('referring_name', $request->input('referring_name', ''));
        }

        if ($request->has('online_opening_date')) {
            $school->setSetting('online_opening_date', $request->input('online_opening_date', ''));
        }

        if ($request->has('online_booking_date')) {
            $school->setSetting('online_booking_date', $request->input('online_booking_date', ''));
        }
        
        return redirect()->route('staff.settings.general')->with('success', 'Impostazioni salvate con successo!');
    }
}
