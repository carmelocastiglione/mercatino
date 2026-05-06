@extends('layouts.app-staff')

@section('title', 'Riepilogo Vendita')

@section('content')
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="mb-8 flex items-center justify-between">
            <div>
                <h1 class="text-4xl font-bold text-gray-900">Riepilogo Vendita</h1>
                <p class="text-gray-600 mt-2">Data: {{ $sale->created_at->format('d/m/Y H:i') }}</p>
            </div>
            <div class="flex gap-2">
                <button onclick="handlePrint()" class="px-6 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition print:hidden">
                    🖨️ Stampa
                </button>
                <a href="{{ route('staff.sales.index') }}" class="px-6 py-3 bg-gray-600 text-white font-medium rounded-lg hover:bg-gray-700 transition print:hidden">
                    ← Torna alle vendite
                </a>
            </div>
        </div>

        <!-- Main Content -->
        <div class="space-y-8">
            <!-- Buyer Information Card -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-8">
                <div class="grid grid-cols-2 gap-8">
                    <div>
                        <p class="text-sm text-gray-600 mb-2">Acquirente</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $sale->buyer->name }} {{ $sale->buyer->surname }}</p>
                        <p class="text-sm text-gray-600 mt-1">Codice: {{ $sale->buyer->code }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 mb-2">Email</p>
                        <p class="text-lg text-gray-900">{{ $sale->buyer->email }}</p>
                    </div>
                </div>
            </div>

            <!-- Book Details -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-8 py-6 border-b border-gray-200 bg-gray-50">
                    <h2 class="text-2xl font-bold text-gray-900">Libro Venduto</h2>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900">Titolo</th>
                                <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900">Autore</th>
                                <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900">Condizione</th>
                                <th class="px-6 py-4 text-right text-sm font-semibold text-gray-900">Prezzo</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 text-sm text-gray-900 font-medium">
                                    {{ $sale->bookListing->book->title }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    {{ $sale->bookListing->book->author }}
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold
                                        @switch($sale->bookListing->condition)
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
                                        @switch($sale->bookListing->condition)
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
                                    €{{ number_format($sale->bookListing->price, 2) }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Transaction Details -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <p class="text-sm text-gray-600 mb-2">Data Vendita</p>
                <p class="text-xl font-bold text-gray-900">{{ $sale->created_at->format('d/m/Y H:i') }}</p>
            </div>

            <!-- Total Amount -->
            <div class="bg-gradient-to-r from-green-50 to-green-100 border-2 border-green-300 rounded-lg p-8">
                <div class="text-right">
                    <p class="text-sm text-gray-600 mb-2">Importo Vendita</p>
                    <p class="text-4xl font-bold text-green-600">€{{ number_format($sale->bookListing->price, 2) }}</p>
                </div>
            </div>

            <!-- Registered By -->
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-6">
                <p class="text-sm text-gray-600 mb-2">Registrato da</p>
                <p class="text-lg font-medium text-gray-900">{{ $sale->soldBy->name }} {{ $sale->soldBy->surname }}</p>
            </div>
        </div>

        <!-- Print Styles -->
        <style media="print">
            body {
                background: white;
            }
            .print\:hidden {
                display: none !important;
            }
        </style>
    </div>

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
                    <title>Riepilogo Vendita</title>
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
@endsection
