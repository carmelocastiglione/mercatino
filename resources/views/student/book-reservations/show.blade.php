@extends('layouts.app-student')

@section('title', 'Riepilogo Prenotazione Libri')

@section('content')
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="mb-8 flex items-center justify-between">
            <div>
                <h1 class="text-4xl font-bold text-gray-900">Riepilogo Prenotazione</h1>
                <p class="text-gray-600 mt-2">Data: {{ $batch->created_at->format('d/m/Y H:i') }}</p>
            </div>
            <div class="flex gap-2 print:hidden">
                <button onclick="handlePrint()" class="px-6 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition">
                    🖨️ Stampa
                </button>
                <a href="{{ route('student.book-reservations.index') }}" class="px-6 py-3 bg-gray-600 text-white font-medium rounded-lg hover:bg-gray-700 transition">
                    ← Torna alle prenotazioni
                </a>
            </div>
        </div>

        <!-- Main Content -->
        <div class="space-y-8">
            <!-- Success Message -->
            @if(session('success'))
                <div class="p-4 bg-green-50 border border-green-300 rounded-lg">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-green-700">{{ session('success') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Student Information Card -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-8">
                <!-- Batch ID - Large -->
                <div class="mb-8 text-center">
                    <p class="text-sm text-gray-600 mb-2">ID Prenotazione</p>
                    <div class="inline-block bg-gradient-to-r from-blue-50 to-blue-100 border-2 border-blue-300 rounded-lg px-8 py-6">
                        <p class="text-5xl font-bold text-blue-600 tracking-widest">#{{ $batch->id }}</p>
                    </div>
                </div>

                <!-- Student Details -->
                <div class="grid grid-cols-2 gap-8 mb-8">
                    <div>
                        <p class="text-sm text-gray-600 mb-2">Nome Studente</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $batch->user->name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 mb-2">Email</p>
                        <p class="text-lg text-gray-900">{{ $batch->user->email }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-8">
                    <div>
                        <p class="text-sm text-gray-600 mb-2">Scuola</p>
                        <p class="text-lg font-medium text-gray-900">{{ $batch->school->name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 mb-2">Data Prenotazione</p>
                        <p class="text-lg font-medium text-gray-900">{{ $batch->created_at->format('d/m/Y') }}</p>
                    </div>
                </div>

                @if($batch->notes)
                    <div class="mt-8">
                        <p class="text-sm text-gray-600 mb-2">Note</p>
                        <p class="text-lg text-gray-900">{{ $batch->notes }}</p>
                    </div>
                @endif
            </div>

            <!-- Books Table -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-8 py-6 border-b border-gray-200 bg-gray-50">
                    <h2 class="text-2xl font-bold text-gray-900">Libri Prenotati</h2>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900">#</th>
                                <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900">Titolo</th>
                                <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900">Autore</th>
                                <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900">ISBN</th>
                                <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900">Condizione</th>
                                <th class="px-6 py-4 text-right text-sm font-semibold text-gray-900">Prezzo</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($batch->bookReservations as $index => $reservation)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $index + 1 }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-900 font-medium">
                                        {{ $reservation->bookListing->book->title }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600">
                                        {{ $reservation->bookListing->book->author }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600 font-mono">
                                        {{ $reservation->bookListing->book->isbn }}
                                    </td>
                                    <td class="px-6 py-4 text-sm">
                                        <span class="px-3 py-1 rounded-full text-xs font-semibold
                                            @switch($reservation->bookListing->condition)
                                                @case('like-new')
                                                    bg-green-100 text-green-800
                                                    @break
                                                @case('good')
                                                    bg-blue-100 text-blue-800
                                                    @break
                                                @case('fair')
                                                    bg-yellow-100 text-yellow-800
                                                    @break
                                                @case('poor')
                                                    bg-red-100 text-red-800
                                                    @break
                                            @endswitch
                                        ">
                                            @switch($reservation->bookListing->condition)
                                                @case('like-new')
                                                    Come Nuovo
                                                    @break
                                                @case('good')
                                                    Buona
                                                    @break
                                                @case('fair')
                                                    Discreta
                                                    @break
                                                @case('poor')
                                                    Scarsa
                                                    @break
                                            @endswitch
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm font-semibold text-gray-900 text-right">
                                        €{{ number_format($reservation->bookListing->price, 2) }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                        Nessun libro in questa prenotazione
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Summary Footer -->
            <div class="bg-gradient-to-r from-blue-50 to-blue-100 border-2 border-blue-300 rounded-lg p-8">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-sm text-gray-600 mb-2">Numero Libri</p>
                        <p class="text-4xl font-bold text-gray-900">{{ $batch->bookReservations()->count() }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm text-gray-600 mb-2">Totale Prenotazione</p>
                        <p class="text-4xl font-bold text-blue-600">€{{ number_format($batch->bookReservations->sum(fn($r) => $r->bookListing->price), 2) }}</p>
                    </div>
                </div>
            </div>

            <!-- Status Summary -->
            <div class="grid grid-cols-2 gap-6">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <p class="text-sm text-gray-600 mb-2">Prenotazioni In Sospeso</p>
                    <p class="text-3xl font-bold text-yellow-600">
                        {{ $batch->bookReservations->where('status', 'pending')->count() }}
                    </p>
                </div>
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <p class="text-sm text-gray-600 mb-2">Prenotazioni Confermate</p>
                    <p class="text-3xl font-bold text-green-600">
                        {{ $batch->bookReservations->where('status', 'confirmed')->count() }}
                    </p>
                </div>
            </div>

            <!-- Information Note -->
            <div class="bg-blue-50 border-2 border-blue-300 rounded-lg p-6 mt-8">
                <p class="text-sm font-bold text-blue-900 mb-3">ℹ️ INFORMAZIONI PRENOTAZIONE</p>
                <p class="text-sm text-blue-800 leading-relaxed">
                    La tua prenotazione è stata registrata e sarà esaminata dallo staff della scuola. Riceverai una notifica quando lo stato della tua prenotazione cambierà. Grazie!
                </p>
            </div>

            <!-- Footer Actions -->
            <div class="flex gap-4 print:hidden">
                <a href="{{ route('student.book-reservations.index') }}" class="flex-1 px-6 py-4 bg-gray-600 text-white font-medium rounded-lg hover:bg-gray-700 transition text-center">
                    ← Torna alle prenotazioni
                </a>
                <button onclick="handlePrint()" class="flex-1 px-6 py-4 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition">
                    🖨️ Stampa Riepilogo
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
                        <title>Riepilogo Prenotazione Libri</title>
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
                                * {
                                    color: black !important;
                                    background: white !important;
                                    box-shadow: none !important;
                                }
                                .no-print {
                                    display: none !important;
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
