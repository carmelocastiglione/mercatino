<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Services\ExportService;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportController extends Controller
{
    /**
     * Mostra la pagina di esportazione con la lista delle opzioni
     */
    public function index(): View
    {
        $exportTypes = ExportService::getAvailableTypes();
        
        return view('staff.export.index', [
            'exportTypes' => $exportTypes,
        ]);
    }

    /**
     * Scarica il CSV del tipo richiesto
     */
    public function download(string $type): StreamedResponse
    {
        $schoolId = auth()->user()->school_id;
        
        return ExportService::exportToCsv($type, $schoolId);
    }
}
