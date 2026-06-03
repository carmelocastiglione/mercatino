@extends('layouts.app-staff')

@section('title', 'Riepilogo Ritiri e Archiviazioni')

@section('content')
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="mb-8 flex items-center justify-between">
            <div>
                <h1 class="text-4xl font-bold text-gray-900">Riepilogo Ritiri/Archiviazioni</h1>
                <p class="text-gray-600 mt-2">Data: {{ $batch->created_at->format('d/m/Y H:i') }}</p>
            </div>
            <div class="flex gap-2 print:hidden">
                <button onclick="handlePrint()" class="px-6 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition">
                    Stampa
                </button>
                <a href="{{ route('staff.withdrawals.process-seller', $batch->user->id) }}" class="px-6 py-3 bg-gray-600 text-white font-medium rounded-lg hover:bg-gray-700 transition">
                    Torna a Gestione Ritiri
                </a>
            </div>
        </div>

        <!-- Main Content -->
        <div class="space-y-8">
            <!-- Pickup Batch Information Card -->
            <div class="grid grid-cols-3 gap-6">
                <!-- Card: ID, Seller Code, Dates -->
                <div class="col-span-2 bg-white rounded-lg shadow-sm border border-gray-200 p-8">
                    <div class="space-y-6">
                        <!-- ID and Seller Code Row -->
                        <div class="grid grid-cols-2 gap-6">
                            <div>
                                <p class="text-xs text-gray-500 font-semibold uppercase mb-1 text-center">ID Operazione</p>
                                <div class="w-full bg-gradient-to-br from-amber-50 to-amber-100 border-2 border-amber-300 rounded-lg p-4 text-center">
                                    <p class="text-3xl font-bold text-amber-600 tracking-widest">#{{ $batch->id }}</p>
                                </div>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 font-semibold uppercase mb-1 text-center">Codice Venditore</p>
                                <div class="w-full bg-gradient-to-br from-amber-50 to-amber-100 border-2 border-amber-300 rounded-lg p-4 text-center">
                                    <p class="text-3xl font-bold text-amber-600 tracking-widest">{{ $batch->user->code }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Dates -->
                        <div class="space-y-4 pt-4 border-t border-gray-200">
                            <div class="text-center">
                                <p class="text-xs text-gray-500 font-semibold uppercase mb-1">Data Operazione</p>
                                <p class="text-sm font-medium text-gray-900">{{ $batch->created_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card: Seller Details -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-8">
                    <div class="space-y-4 flex flex-col justify-center items-center h-full text-center">
                        <div>
                            <p class="text-xs text-gray-500 font-semibold uppercase mb-1">Venditore</p>
                            <p class="text-lg font-bold text-gray-900">{{ $batch->user->name }} {{ $batch->user->surname }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 font-semibold uppercase mb-1">Email</p>
                            <p class="text-sm text-gray-700">{{ $batch->user->email }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 font-semibold uppercase mb-1">Numero Libri</p>
                            <p class="text-lg font-bold text-gray-900">{{ $batch->pickups->count() }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Libri Ritirati/Archiviati Table -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-8 py-6 border-b border-gray-200 bg-gray-50">
                    <h2 class="text-2xl font-bold text-gray-900">Dettagli Libri</h2>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900">#</th>
                                <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900">Titolo</th>
                                <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900">ISBN</th>
                                <th class="px-6 py-4 text-center text-sm font-semibold text-gray-900">Stato</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($batch->pickups as $index => $pickup)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $index + 1 }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-900 font-medium">
                                        {{ $pickup->bookListing->book->title }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600">
                                        {{ $pickup->bookListing->book->isbn ?? 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 text-center text-sm font-semibold">
                                        @if($pickup->leave)
                                            <span class="px-3 py-1 rounded text-white text-xs font-semibold bg-gray-600">Archiviato</span>
                                        @else
                                            <span class="px-3 py-1 rounded text-white text-xs font-semibold bg-red-600">Ritirato</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-8 text-center text-gray-500">
                                        Nessun libro
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Summary Footer -->
            <div class="bg-gradient-to-r from-amber-50 to-amber-100 border-2 border-amber-300 rounded-lg p-8">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-sm text-gray-600 mb-2">Totale Libri</p>
                        <p class="text-4xl font-bold text-gray-900">{{ $batch->pickups->count() }}</p>
                    </div>
                    <div class="flex gap-12">
                        <div class="text-center">
                            <p class="text-sm text-red-600 font-semibold mb-2">Ritirati</p>
                            <p class="text-4xl font-bold text-red-600">{{ $batch->pickups->where('leave', false)->count() }}</p>
                        </div>
                        <div class="text-center">
                            <p class="text-sm text-gray-600 font-semibold mb-2">Archiviati</p>
                            <p class="text-4xl font-bold text-gray-900">{{ $batch->pickups->where('leave', true)->count() }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Information Note -->
            <x-information-note message="L'operazione di ritiro/archiviazione è stata registrata correttamente. Stampa questo riepilogo e conserva una copia come documentazione ufficiale della transazione." />

            <!-- Action Buttons -->
            <div class="flex gap-4 print:hidden">
                <a href="{{ route('staff.withdrawals.process-seller', $batch->user->id) }}" class="flex-1 px-6 py-4 bg-gray-600 text-white font-medium rounded-lg hover:bg-gray-700 transition text-center">
                    Torna a Gestione Ritiri
                </a>
                <button onclick="handlePrint()" class="flex-1 px-6 py-4 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition">
                    Stampa Riepilogo
                </button>
            </div>
        </div>

        <!-- Print Styles -->
        <style media="print">
            @page {
                margin: 0.5cm;
                size: A4;
            }

            html, body {
                width: 100%;
                height: 100%;
                margin: 0;
                padding: 0;
                background: white;
            }

            body {
                overflow: visible !important;
            }

            /* Hide ALL navigation and sidebar elements */
            nav,
            aside,
            [class*="sidebar"],
            [class*="navbar"],
            [class*="header"],
            [class*="fixed"],
            [style*="fixed"],
            [style*="position: fixed"],
            .print\:hidden {
                display: none !important;
            }

            /* Hide body padding/margins that might show sidebar */
            body > * {
                margin: 0 !important;
                padding: 0 !important;
            }

            /* Ensure main content area */
            main {
                margin: 0 !important;
                padding: 0 !important;
                max-width: 100% !important;
                width: 100% !important;
            }

            /* Content area */
            .max-w-4xl {
                max-width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
            }

            /* Optimize tables */
            table {
                page-break-inside: avoid;
                width: 100%;
            }

            tr {
                page-break-inside: avoid;
            }

            /* Reduce font sizes for print */
            h1 { font-size: 1.25rem !important; line-height: 1.3; }
            h2 { font-size: 1rem !important; line-height: 1.3; }
            h3 { font-size: 0.9rem !important; line-height: 1.3; }
            p { font-size: 0.85rem !important; line-height: 1.3; }
            td, th { font-size: 0.85rem !important; }
            span { font-size: 0.85rem !important; }

            /* Reduce spacing */
            .mb-8 { margin-bottom: 0.5rem !important; }
            .mb-6 { margin-bottom: 0.4rem !important; }
            .mb-4 { margin-bottom: 0.3rem !important; }
            .mb-3 { margin-bottom: 0.2rem !important; }
            .mb-2 { margin-bottom: 0.1rem !important; }
            .mt-2 { margin-top: 0.1rem !important; }
            .mt-4 { margin-top: 0.2rem !important; }

            .space-y-8 > * + * { margin-top: 0.5rem !important; }
            .space-y-6 > * + * { margin-top: 0.4rem !important; }
            .space-y-4 > * + * { margin-top: 0.3rem !important; }

            /* Reduce padding */
            .p-8 { padding: 0.5rem !important; }
            .p-6 { padding: 0.4rem !important; }
            .p-4 { padding: 0.3rem !important; }
            .px-6 { padding-left: 0.4rem !important; padding-right: 0.4rem !important; }
            .py-4 { padding-top: 0.2rem !important; padding-bottom: 0.2rem !important; }
            .py-3 { padding-top: 0.2rem !important; padding-bottom: 0.2rem !important; }

            /* Reduce gap */
            .gap-6 { gap: 0.4rem !important; }
            .gap-4 { gap: 0.3rem !important; }

            /* Table optimization */
            table {
                margin-bottom: 0.3rem !important;
            }

            th, td {
                padding: 0.3rem !important;
            }

            /* Remove background colors and rounded corners from badges only */
            span.rounded {
                background-color: transparent !important;
                border-radius: 0 !important;
            }

            /* Clean colors for print */
            * {
                color: black !important;
                background: white !important;
                box-shadow: none !important;
                border-color: black !important;
            }

            a {
                text-decoration: none !important;
                color: black !important;
            }
        </style>

        <script>
            function handlePrint() {
                // Apri una nuova tab
                const printWindow = window.open('', '_blank');
                
                // Ottieni il contenuto principale (escluso header con pulsanti)
                const content = document.querySelector('.max-w-4xl').innerHTML;
                
                // Scrivi l'HTML nella nuova tab con stili
                printWindow.document.write(`
                    <!DOCTYPE html>
                    <html>
                    <head>
                        <meta charset="UTF-8">
                        <meta name="viewport" content="width=device-width, initial-scale=1.0">
                        <title>Riepilogo Ritiri/Archiviazioni</title>
                        <script src="https://cdn.tailwindcss.com"><\/script>
                        <style>
                            body {
                                font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
                                padding: 40px;
                                background: white;
                            }
                            .print-content {
                                max-width: 900px;
                                margin: 0 auto;
                            }
                            @media print {
                                body {
                                    padding: 0;
                                }
                                @page {
                                    margin: 0.5cm;
                                    size: A4;
                                }
                            }
                        </style>
                    </head>
                    <body>
                        <div class="print-content">
                            ${content}
                        </div>
                        <script>
                            // Nascondi il header con i pulsanti quando la tab si apre
                            setTimeout(() => {
                                const header = document.querySelector('.mb-8.flex');
                                if (header) {
                                    header.style.display = 'none';
                                }
                                window.print();
                            }, 500);

                            // Chiudi la tab quando il dialogo di stampa si chiude (stampa o annulla)
                            window.addEventListener('afterprint', () => {
                                window.close();
                            });
                        <\/script>
                    </body>
                    </html>
                `);
                printWindow.document.close();
            }
        </script>
    </div>
@endsection
