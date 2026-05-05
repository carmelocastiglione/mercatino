@extends('layouts.app-staff')

@section('title', 'Riepilogo Acquisizione')

@section('content')
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="mb-8 flex items-center justify-between">
            <div>
                <h1 class="text-4xl font-bold text-gray-900">Riepilogo Acquisizione</h1>
                <p class="text-gray-600 mt-2">Data: {{ $acquisition->created_at->format('d/m/Y H:i') }}</p>
            </div>
            <div class="flex gap-2">
                <button onclick="handlePrint()" class="px-6 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition print:hidden">
                    🖨️ Stampa
                </button>
                <a href="{{ route('staff.acquisitions.index') }}" class="px-6 py-3 bg-gray-600 text-white font-medium rounded-lg hover:bg-gray-700 transition print:hidden">
                    ← Torna alle acquisizioni
                </a>
            </div>
        </div>

        <!-- Main Content -->
        <div class="space-y-8">
            <!-- Seller Information Card -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-8">
                <!-- Seller Code - Large -->
                <div class="mb-8 text-center">
                    <p class="text-sm text-gray-600 mb-2">Codice Venditore</p>
                    <div class="inline-block bg-gradient-to-r from-blue-50 to-blue-100 border-2 border-blue-300 rounded-lg px-8 py-6">
                        <p class="text-5xl font-bold text-blue-600 tracking-widest">{{ $acquisition->seller->code }}</p>
                    </div>
                </div>

                <!-- Seller Details -->
                <div class="grid grid-cols-2 gap-8 mb-8">
                    <div>
                        <p class="text-sm text-gray-600 mb-2">Nome Venditore</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $acquisition->seller->name }} {{ $acquisition->seller->surname }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 mb-2">Email</p>
                        <p class="text-lg text-gray-900">{{ $acquisition->seller->email }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-8">
                    <div>
                        <p class="text-sm text-gray-600 mb-2">Registrato da</p>
                        <p class="text-lg font-medium text-gray-900">{{ $acquisition->staff->name }} {{ $acquisition->staff->surname }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 mb-2">Data Acquisizione</p>
                        <p class="text-lg font-medium text-gray-900">{{ $acquisition->created_at->format('d/m/Y') }}</p>
                    </div>
                </div>
            </div>

            <!-- Books Table -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-8 py-6 border-b border-gray-200 bg-gray-50">
                    <h2 class="text-2xl font-bold text-gray-900">Libri Acquisiti</h2>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900">#</th>
                                <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900">Titolo</th>
                                <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900">Autore</th>
                                <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900">Condizione</th>
                                <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900">Ceduto</th>
                                <th class="px-6 py-4 text-right text-sm font-semibold text-gray-900">Prezzo</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($acquisition->bookListings as $index => $listing)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $index + 1 }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-900 font-medium">
                                        {{ $listing->book->title }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600">
                                        {{ $listing->book->author }}
                                    </td>
                                    <td class="px-6 py-4 text-sm">
                                        <span class="px-3 py-1 rounded-full text-xs font-semibold
                                            @switch($listing->condition)
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
                                            @switch($listing->condition)
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
                                    <td class="px-6 py-4 text-sm">
                                        @if($listing->leave)
                                            <span class="px-3 py-1 rounded-full text-xs font-semibold bg-orange-100 text-orange-800">
                                                ✓ Sì
                                            </span>
                                        @else
                                            <span class="px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-800">
                                                ✗ No
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-sm font-semibold text-gray-900 text-right">
                                        €{{ number_format($listing->price, 2) }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                        Nessun libro in questa acquisizione
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
                        <p class="text-4xl font-bold text-gray-900">{{ $acquisition->bookListings->count() }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm text-gray-600 mb-2">Totale Acquisizione</p>
                        <p class="text-4xl font-bold text-blue-600">€{{ number_format($acquisition->total_price, 2) }}</p>
                    </div>
                </div>
            </div>

            <!-- Ceduti Summary -->
            <div class="grid grid-cols-2 gap-6">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <p class="text-sm text-gray-600 mb-2">Libri da Ritirare</p>
                    <p class="text-3xl font-bold text-gray-900">
                        {{ $acquisition->bookListings->where('leave', false)->count() }}
                    </p>
                </div>
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <p class="text-sm text-gray-600 mb-2">Libri da Cedere se Invenduti</p>
                    <p class="text-3xl font-bold text-orange-600">
                        {{ $acquisition->bookListings->where('leave', true)->count() }}
                    </p>
                </div>
            </div>

            <!-- Disclaimer -->
            <div class="bg-red-50 border-2 border-red-300 rounded-lg p-6 mt-8">
                <p class="text-sm font-bold text-red-900 mb-3">⚠️ RITIRO INCASSO E INVENDUTO</p>
                <p class="text-sm text-red-800 leading-relaxed">
                    Presentarsi con questo foglio il giorno del ritiro. Al termine di questa giornata l'incasso, i camici e i libri non ritirati verranno donati alla scuola. Per info <span class="font-semibold">cgviganomerate@gmail.com</span>
                </p>
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
                        <title>Riepilogo Acquisizione</title>
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
