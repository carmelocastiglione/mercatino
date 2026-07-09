@extends('layouts.app-staff')

@section('title', 'Riepilogo Processamento Completo')

@section('content')
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4 print:hidden">
            <div>
                <h1 class="text-4xl font-bold text-gray-900">Riepilogo Processamento Completo</h1>
                <p class="text-gray-600 mt-2">Data: {{ now()->format('d/m/Y H:i') }}</p>
            </div>
            <div class="flex flex-col md:flex-row gap-2">
                <button onclick="handlePrint()" class="px-6 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition text-center">
                    Stampa
                </button>
                <a href="{{ route('staff.withdrawals.process-seller', $seller->id) }}" class="px-6 py-3 bg-gray-600 text-white font-medium rounded-lg hover:bg-gray-700 transition text-center">
                    Torna a Gestione Ritiri
                </a>
            </div>
        </div>

        <div class="space-y-8">
            <!-- ==================== PAGE 1: WITHDRAWAL BATCH SUMMARY ==================== -->
            @if($withdrawCount > 0 && $withdrawalBatch)
                <div class="page-break-container">
                    <!-- Withdrawal Batch Information Card -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Card: ID, Seller Code, Dates -->
                        <div class="md:col-span-2 bg-white rounded-lg shadow-sm border border-gray-200 p-8">
                            <div class="space-y-6">
                                <!-- ID and Seller Code Row -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div class="flex flex-col">
                                        <p class="text-xs text-gray-500 font-semibold uppercase mb-1 text-center">Codice Transazione</p>
                                        <div class="flex-1 w-full bg-white border-2 border-purple-300 rounded-lg p-4 text-center flex flex-col justify-center items-center">
                                            @if($withdrawalBatch->ean13)
                                                <div class="flex justify-center">
                                                    <?php
                                                        $barcode = (new \Picqer\Barcode\Types\TypeEan13())->getBarcode($withdrawalBatch->ean13);
                                                        $renderer = new \Picqer\Barcode\Renderers\SvgRenderer();
                                                        echo $renderer->render($barcode);
                                                    ?>
                                                </div>
                                                <p class="text-sm font-mono font-bold text-gray-900 mt-2">{{ $withdrawalBatch->ean13 }}</p>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="flex flex-col">
                                        <p class="text-xs text-gray-500 font-semibold uppercase mb-1 text-center">Codice Venditore</p>
                                        <div class="flex-1 w-full bg-gradient-to-br from-purple-50 to-purple-100 border-2 border-purple-300 rounded-lg p-4 text-center flex items-center justify-center">
                                            <p class="text-3xl font-bold text-purple-600 tracking-widest">{{ $withdrawalBatch->user->code }}</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Dates -->
                                <div class="space-y-4 pt-4 border-t border-gray-200">
                                    <div class="text-center">
                                        <p class="text-xs text-gray-500 font-semibold uppercase mb-1">Data Riscossione</p>
                                        <p class="text-sm font-medium text-gray-900">{{ $withdrawalBatch->created_at->format('d/m/Y H:i') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Card: Seller Details -->
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-8">
                            <div class="space-y-4 flex flex-col justify-center items-center h-full text-center">
                                <div>
                                    <p class="text-xs text-gray-500 font-semibold uppercase mb-1">Venditore</p>
                                    <p class="text-lg font-bold text-gray-900">{{ $withdrawalBatch->user->name }} {{ $withdrawalBatch->user->surname }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 font-semibold uppercase mb-1">Email</p>
                                    <p class="text-sm text-gray-700">{{ $withdrawalBatch->user->email }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 font-semibold uppercase mb-1">Numero Libri</p>
                                    <p class="text-lg font-bold text-gray-900">{{ $withdrawalBatch->withdrawals->count() }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Libri Ritirati Table -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden mt-8">
                        <div class="px-8 py-6 border-b border-gray-200 bg-gray-50">
                            <h2 class="text-2xl font-bold text-gray-900">Importo Riscosso</h2>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead class="bg-gray-50 border-b border-gray-200">
                                    <tr>
                                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900">#</th>
                                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900">Titolo</th>
                                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900">ISBN</th>
                                        <th class="px-6 py-4 text-right text-sm font-semibold text-gray-900">Importo</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @forelse($withdrawalBatch->withdrawals as $index => $withdrawal)
                                        <tr class="hover:bg-gray-50 transition">
                                            <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $index + 1 }}</td>
                                            <td class="px-6 py-4 text-sm text-gray-900 font-medium">
                                                {{ $withdrawal->bookListing->book->title }}
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-600">
                                                {{ $withdrawal->bookListing->book->isbn ?? 'N/A' }}
                                            </td>
                                            <td class="px-6 py-4 text-sm font-semibold text-gray-900 text-right">
                                                €{{ number_format($withdrawal->amount, 2, ',', '.') }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="px-6 py-8 text-center text-gray-500">
                                                Nessun libro ritirati
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Summary Footer -->
                    <div class="bg-gradient-to-r from-green-50 to-green-100 border-2 border-green-300 rounded-lg p-8 mt-8">
                        <div class="flex justify-between items-center">
                            <div>
                                <p class="text-sm text-gray-600 mb-2">Numero Libri</p>
                                <p class="text-4xl font-bold text-gray-900">{{ $withdrawalBatch->withdrawals->count() }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm text-gray-600 mb-2">Importo Totale Riscosso</p>
                                <p class="text-4xl font-bold text-green-600">€{{ number_format($withdrawalBatch->total_amount, 2, ',', '.') }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Information Note -->
                    <div class="mt-8 print:hidden">
                        <x-information-note message="La riscossione è stata registrata correttamente. Stampa questo riepilogo e conserva una copia come documentazione ufficiale della transazione." />
                    </div>
                </div>
            @endif

            <!-- ==================== PAGE 2: PICKUP BATCH SUMMARY ==================== -->
            @if($archivedCount > 0 && $pickupBatch)
                <!-- Page break - visible only in print -->
                <div class="hidden print:block" style="page-break-before: always;"></div>
                
                <div class="page-break-container">
                    <!-- Pickup Batch Information Card -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Card: ID, Seller Code, Dates -->
                        <div class="md:col-span-2 bg-white rounded-lg shadow-sm border border-gray-200 p-8">
                            <div class="space-y-6">
                                <!-- ID and Seller Code Row -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div class="flex flex-col">
                                        <p class="text-xs text-gray-500 font-semibold uppercase mb-1 text-center">Codice Transazione</p>
                                        <div class="flex-1 w-full bg-white border-2 border-amber-300 rounded-lg p-4 text-center flex flex-col justify-center items-center">
                                            @if($pickupBatch->ean13)
                                                <div class="flex justify-center">
                                                    <?php
                                                        $barcode = (new \Picqer\Barcode\Types\TypeEan13())->getBarcode($pickupBatch->ean13);
                                                        $renderer = new \Picqer\Barcode\Renderers\SvgRenderer();
                                                        echo $renderer->render($barcode);
                                                    ?>
                                                </div>
                                                <p class="text-sm font-mono font-bold text-gray-900 mt-2">{{ $pickupBatch->ean13 }}</p>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="flex flex-col">
                                        <p class="text-xs text-gray-500 font-semibold uppercase mb-1 text-center">Codice Venditore</p>
                                        <div class="flex-1 w-full bg-gradient-to-br from-amber-50 to-amber-100 border-2 border-amber-300 rounded-lg p-4 text-center flex items-center justify-center">
                                            <p class="text-3xl font-bold text-amber-600 tracking-widest">{{ $pickupBatch->user->code }}</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Dates -->
                                <div class="space-y-4 pt-4 border-t border-gray-200">
                                    <div class="text-center">
                                        <p class="text-xs text-gray-500 font-semibold uppercase mb-1">Data Operazione</p>
                                        <p class="text-sm font-medium text-gray-900">{{ $pickupBatch->created_at->format('d/m/Y H:i') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Card: Seller Details -->
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-8">
                            <div class="space-y-4 flex flex-col justify-center items-center h-full text-center">
                                <div>
                                    <p class="text-xs text-gray-500 font-semibold uppercase mb-1">Venditore</p>
                                    <p class="text-lg font-bold text-gray-900">{{ $pickupBatch->user->name }} {{ $pickupBatch->user->surname }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 font-semibold uppercase mb-1">Email</p>
                                    <p class="text-sm text-gray-700">{{ $pickupBatch->user->email }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 font-semibold uppercase mb-1">Numero Libri</p>
                                    <p class="text-lg font-bold text-gray-900">{{ $pickupBatch->pickups->count() }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Libri Ritirati/Archiviati Table -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden mt-8">
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
                                    @forelse($pickupBatch->pickups as $index => $pickup)
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
                    <div class="bg-gradient-to-r from-amber-50 to-amber-100 border-2 border-amber-300 rounded-lg p-8 mt-8">
                        <div class="flex justify-between items-center">
                            <div>
                                <p class="text-sm text-gray-600 mb-2">Totale Libri</p>
                                <p class="text-4xl font-bold text-gray-900">{{ $pickupBatch->pickups->count() }}</p>
                            </div>
                            <div class="flex gap-12">
                                <div class="text-center">
                                    <p class="text-sm text-red-600 font-semibold mb-2">Ritirati</p>
                                    <p class="text-4xl font-bold text-red-600">{{ $pickupBatch->pickups->where('leave', false)->count() }}</p>
                                </div>
                                <div class="text-center">
                                    <p class="text-sm text-gray-600 font-semibold mb-2">Archiviati</p>
                                    <p class="text-4xl font-bold text-gray-900">{{ $pickupBatch->pickups->where('leave', true)->count() }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Information Note -->
                    <div class="mt-8 print:hidden">
                        <x-information-note message="L'operazione di ritiro/archiviazione è stata registrata correttamente. Stampa questo riepilogo e conserva una copia come documentazione ufficiale della transazione." />
                    </div>
                </div>
            @endif
        </div>

        <!-- Action Buttons -->
        <div class="flex gap-4 print:hidden mt-8">
            <a href="{{ route('staff.withdrawals.process-seller', $seller->id) }}" class="flex-1 px-6 py-4 bg-gray-600 text-white font-medium rounded-lg hover:bg-gray-700 transition text-center">
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

        /* FORCE RESPONSIVE GRID LAYOUT FOR PRINT */
        .grid.md\:grid-cols-3 {
            display: grid !important;
            grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
        }

        .grid.md\:grid-cols-2 {
            display: grid !important;
            grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
        }

        .md\:col-span-2 {
            grid-column: span 2 !important;
        }

        .flex.md\:flex-row {
            flex-direction: row !important;
        }

        .md\:items-center {
            align-items: center !important;
        }

        .md\:justify-between {
            justify-content: space-between !important;
        }

        /* Optimize tables */
        table {
            page-break-inside: avoid;
            width: 100%;
        }

        tr {
            page-break-inside: avoid;
        }

        /* Page break handling */
        .page-break-container {
            page-break-after: auto;
            page-break-inside: avoid;
        }

        .page-break {
            page-break-after: always;
            page-break-before: auto;
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
        .mt-8 { margin-top: 0.5rem !important; }

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
        .gap-12 { gap: 0.8rem !important; }

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
            window.print();
        }
    </script>
@endsection
