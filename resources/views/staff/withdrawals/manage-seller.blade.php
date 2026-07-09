@extends('layouts.app-staff')

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="px-4 py-6 sm:px-0">
        <!-- Header -->
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">
                    Gestione Ritiri - {{ $seller->name }} {{ $seller->surname }}
                </h1>
                <p class="text-gray-600 text-sm mt-1">{{ $seller->email }} ({{ $seller->code }})</p>
            </div>
            <a href="{{ route('staff.withdrawals.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white font-semibold py-2 px-4 rounded">
                ← Torna alla lista
            </a>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-3 gap-6 mb-8">
            <x-stats-card label="Totale Vendite" :value="$seller->getTotalSalesAmount()" color="green" formatted />
            <x-stats-card label="Già Riscosso" :value="$seller->getTotalWithdrawnAmount()" color="red" formatted />
            <x-stats-card label="Da Riscuotere" :value="$seller->getAvailableBalance()" color="blue" formatted />
        </div>

        <!-- Navigation Links -->
        <div class="grid grid-cols-4 gap-3 mb-8">
            <a href="#libri-venduti" class="flex items-center justify-center gap-2 px-4 py-4 bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white font-semibold rounded-lg shadow-md hover:shadow-lg hover:scale-105 transition-all duration-200">
                <span>Libri Venduti</span>
                @if(count($soldBooks) > 0)
                    <span class="flex items-center justify-center w-6 h-6 bg-red-600 text-white text-xs font-bold rounded-full">
                        {{ count($soldBooks) }}
                    </span>
                @endif
            </a>
            <a href="#storico-riscossioni" class="px-4 py-4 bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white font-semibold rounded-lg shadow-md hover:shadow-lg hover:scale-105 transition-all duration-200 text-center">
                Storico Riscossioni
            </a>
            <a href="#libri-non-venduti" class="flex items-center justify-center gap-2 px-4 py-4 bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white font-semibold rounded-lg shadow-md hover:shadow-lg hover:scale-105 transition-all duration-200">
                <span>Libri Non Venduti</span>
                @if(count($unsoldBooks) > 0)
                    <span class="flex items-center justify-center w-6 h-6 bg-red-600 text-white text-xs font-bold rounded-full">
                        {{ count($unsoldBooks) }}
                    </span>
                @endif
            </a>
            <a href="#storico-invenduti" class="px-4 py-4 bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white font-semibold rounded-lg shadow-md hover:shadow-lg hover:scale-105 transition-all duration-200 text-center">
                Storico Invenduti
            </a>
        </div>

        <!-- Complete Withdrawal Button -->
        @if(count($soldBooks) > 0 || count($unsoldBooks) > 0)
            <div class="mb-8">
                <form action="{{ route('staff.withdrawals.process-complete', $seller->id) }}" method="POST" onsubmit="return confirm('Sei sicuro? Questo processerà TUTTI i libri venduti e non venduti.');">
                    @csrf
                    <button type="submit" class="w-full px-6 py-4 bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white font-bold text-lg rounded-lg shadow-md hover:shadow-lg transition-all duration-200 flex items-center justify-center gap-2">
                        <span>Processa tutto ({{count($soldBooks) + count($unsoldBooks)}} libri)</span>
                    </button>
                </form>
            </div>
        @endif

        <!-- Messages -->
        @if($errors->any())
            <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6 text-red-700">
                {{ $errors->first('error') }}
            </div>
        @endif

        <!-- Books Venduti Section -->
        <div id="libri-venduti" class="bg-white shadow-md rounded-lg mb-8 scroll-mt-20">
            <div class="border-b border-gray-200 px-6 py-4 flex items-center justify-between">
                <h2 class="text-xl font-bold text-gray-900">
                    Libri Venduti <span class="text-sm text-gray-500 font-normal">({{ count($soldBooks) }})</span>
                </h2>
                @if(count($soldBooks) > 0)
                    <form action="{{ route('staff.withdrawals.withdraw-all-sold-books', $seller->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Sei sicuro di voler ritirare tutti i soldi dei libri venduti?');">
                        @csrf
                        <button type="submit" class="bg-green-600 hover:bg-green-700 text-white text-sm font-bold py-1 px-3 rounded transition-colors">
                            Ritira Tutto
                        </button>
                    </form>
                @endif
            </div>
            
            @if(count($soldBooks) > 0)
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Titolo</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">ISBN</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Stato</th>
                                <th class="px-6 py-3 text-right text-sm font-semibold text-gray-900">Prezzo Acq.</th>
                                <th class="px-6 py-3 text-right text-sm font-semibold text-gray-900">Prezzo Vend.</th>
                                <th class="px-6 py-3 text-center text-sm font-semibold text-gray-900">Azione</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($soldBooks as $book)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 text-sm text-gray-900 font-semibold">{{ $book->book->title }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-600">{{ $book->book->isbn ?? '-' }}</td>
                                    <td class="px-6 py-4 text-center text-sm">
                                        <span class="px-2 py-1 rounded text-white text-xs font-semibold bg-green-600">Venduto</span>
                                    </td>
                                    <td class="px-6 py-4 text-right text-sm text-gray-900 font-semibold">
                                        €{{ number_format($book->price, 2, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4 text-right text-sm font-semibold text-green-600">
                                        {{ $book->price_sell ? '€' . number_format($book->price_sell, 2, ',', '.') : '-' }}
                                    </td>
                                    <td class="px-6 py-4 text-center text-sm">
                                        <form action="{{ route('staff.withdrawals.withdraw-money', $book->id) }}" method="POST" style="display: inline;">
                                            @csrf
                                            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white text-xs font-bold py-1 px-3 rounded transition-colors">
                                                Ritira Soldi
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="p-8 text-center text-gray-500">
                    <p class="text-lg">Nessun libro venduto</p>
                </div>
            @endif
        </div>

        <!-- Storico Riscossioni / Withdrawal Batches -->
        <div id="storico-riscossioni" class="bg-white shadow-md rounded-lg mb-8 scroll-mt-20">
            <div class="border-b border-gray-200 px-6 py-4">
                <h2 class="text-xl font-bold text-gray-900">
                    Storico Riscossioni
                </h2>
            </div>
            
            @if(count($withdrawalBatches) > 0)
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Codice Transazione</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Data Ritiro</th>
                                <th class="px-6 py-3 text-center text-sm font-semibold text-gray-900">Numero Libri</th>
                                <th class="px-6 py-3 text-right text-sm font-semibold text-gray-900">Totale</th>
                                <th class="px-6 py-3 text-center text-sm font-semibold text-gray-900">Azioni</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($withdrawalBatches as $batch)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 text-sm font-mono font-bold text-gray-900">{{ $batch->ean13 ?? 'N/A' }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-900 font-medium">{{ $batch->created_at->format('d/m/Y H:i') }}</td>
                                    <td class="px-6 py-4 text-sm text-center text-gray-900 font-semibold">{{ $batch->withdrawals->count() }}</td>
                                    <td class="px-6 py-4 text-sm text-right text-gray-900 font-semibold">€{{ number_format($batch->total_amount, 2, ',', '.') }}</td>
                                    <td class="px-6 py-4 text-center text-sm">
                                        <div class="flex gap-2 justify-center">
                                            <button onclick="toggleDetails({{ $batch->id }})" class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded transition-colors">
                                                Dettagli
                                            </button>
                                            <a href="{{ route('staff.withdrawals.show-batch', $batch->id) }}" class="px-3 py-1 bg-green-600 hover:bg-green-700 text-white text-xs font-bold rounded transition-colors">
                                                Riepilogo
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <tr id="details-{{ $batch->id }}" class="hidden">
                                    <td colspan="4" class="px-6 py-4">
                                        <div class="bg-gray-50 rounded-lg p-4">
                                            <div class="overflow-x-auto">
                                                <table class="w-full text-xs">
                                                    <thead class="bg-gray-200">
                                                        <tr>
                                                            <th class="px-3 py-2 text-left font-semibold text-gray-900">#</th>
                                                            <th class="px-3 py-2 text-left font-semibold text-gray-900">Libro</th>
                                                            <th class="px-3 py-2 text-left font-semibold text-gray-900">ISBN</th>
                                                            <th class="px-3 py-2 text-right font-semibold text-gray-900">Importo</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody class="divide-y divide-gray-300">
                                                        @foreach($batch->withdrawals as $index => $withdrawal)
                                                            <tr class="hover:bg-gray-100">
                                                                <td class="px-3 py-2 text-gray-900">{{ $index + 1 }}</td>
                                                                <td class="px-3 py-2 text-gray-900 font-medium">{{ $withdrawal->bookListing->book->title }}</td>
                                                                <td class="px-3 py-2 text-gray-600">{{ $withdrawal->bookListing->book->isbn ?? '-' }}</td>
                                                                <td class="px-3 py-2 text-right text-gray-900 font-semibold">€{{ number_format($withdrawal->amount, 2, ',', '.') }}</td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="p-8 text-center text-gray-500">
                    <p class="text-lg">Nessun ritiro registrato</p>
                </div>
            @endif
        </div>

        <script>
            function toggleDetails(batchId) {
                const detailsRow = document.getElementById('details-' + batchId);
                if (detailsRow.classList.contains('hidden')) {
                    detailsRow.classList.remove('hidden');
                } else {
                    detailsRow.classList.add('hidden');
                }
            }
        </script>

        <!-- Books Non Venduti Section -->
        <div id="libri-non-venduti" class="bg-white shadow-md rounded-lg mb-8 scroll-mt-20">
            <div class="border-b border-gray-200 px-6 py-4 flex items-center justify-between">
                <h2 class="text-xl font-bold text-gray-900">
                    Libri Non Venduti <span class="text-sm text-gray-500 font-normal">({{ count($unsoldBooks) }})</span>
                </h2>
                @if(count($unsoldBooks) > 0)
                    <form action="{{ route('staff.withdrawals.withdraw-all-books', $seller->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Sei sicuro di voler ritirare e archiviare tutti i libri non venduti?');">
                        @csrf
                        <button type="submit" class="bg-red-600 hover:bg-red-700 text-white text-sm font-bold py-1 px-3 rounded transition-colors">
                            Ritira e Archivia Tutti
                        </button>
                    </form>
                @endif
            </div>
            
            @if(count($unsoldBooks) > 0)
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Titolo</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">ISBN</th>
                                <th class="px-6 py-3 text-center text-sm font-semibold text-gray-900">Stato</th>
                                <th class="px-6 py-3 text-right text-sm font-semibold text-gray-900">Prezzo Acq.</th>
                                <th class="px-6 py-3 text-right text-sm font-semibold text-gray-900">Prezzo Vend.</th>
                                <th class="px-6 py-3 text-center text-sm font-semibold text-gray-900">Azione</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($unsoldBooks as $book)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 text-sm text-gray-900 font-semibold">{{ $book->book->title }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-600">{{ $book->book->isbn ?? '-' }}</td>
                                    <td class="px-6 py-4 text-center text-sm">
                                        <span class="px-2 py-1 rounded text-white text-xs font-semibold 
                                            @switch($book->status)
                                                @case('available') bg-blue-600 @break
                                                @case('reserved') bg-purple-600 @break
                                                @case('pending') bg-yellow-600 @break
                                                @case('withdrawn') bg-red-600 @break
                                                @default bg-gray-600
                                            @endswitch">
                                            @switch($book->status)
                                                @case('available') Disponibile @break
                                                @case('reserved') Prenotato @break
                                                @case('pending') In Sospeso @break
                                                @case('withdrawn') Ritirato @break
                                                @default {{ $book->status }}
                                            @endswitch
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right text-sm text-gray-900 font-semibold">
                                        €{{ number_format($book->price, 2, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4 text-right text-sm font-semibold text-green-600">
                                        {{ $book->price_sell ? '€' . number_format($book->price_sell, 2, ',', '.') : '-' }}
                                    </td>
                                    <td class="px-6 py-4 text-center text-sm">
                                        @if(!$book->leave)
                                            <form action="{{ route('staff.withdrawals.withdraw-book', $book->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Sei sicuro di voler ritirare questo libro?');">
                                                @csrf
                                                <button type="submit" class="bg-orange-600 hover:bg-orange-700 text-white text-xs font-bold py-1 px-3 rounded transition-colors">
                                                    Ritira Libro
                                                </button>
                                            </form>
                                        @else
                                            <form action="{{ route('staff.withdrawals.archive-book', $book->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Sei sicuro di voler archiviare questo libro?');">
                                                @csrf
                                                <button type="submit" class="bg-gray-600 hover:bg-gray-700 text-white text-xs font-bold py-1 px-3 rounded transition-colors">
                                                    Archivia Libro
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="p-8 text-center text-gray-500">
                    <p class="text-lg">Nessun libro non venduto</p>
                </div>
            @endif
        </div>

        <!-- Storico Invenduti / Pickup Batches -->
        <div id="storico-invenduti" class="bg-white shadow-md rounded-lg mb-8 scroll-mt-20">
            <div class="border-b border-gray-200 px-6 py-4">
                <h2 class="text-xl font-bold text-gray-900">
                    Storico Invenduti
                </h2>
            </div>
            
            @if(count($pickupBatches) > 0)
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Codice Transazione</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Data</th>
                                <th class="px-6 py-3 text-center text-sm font-semibold text-gray-900">Numero Libri</th>
                                <th class="px-6 py-3 text-center text-sm font-semibold text-gray-900">Azioni</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($pickupBatches as $batch)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 text-sm font-mono font-bold text-gray-900">{{ $batch->ean13 ?? 'N/A' }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-900 font-medium">{{ $batch->created_at->format('d/m/Y H:i') }}</td>
                                    <td class="px-6 py-4 text-sm text-center text-gray-900 font-semibold">{{ $batch->pickups->count() }}</td>
                                    <td class="px-6 py-4 text-center text-sm space-x-2">
                                        <button onclick="togglePickupDetails({{ $batch->id }})" class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded transition-colors">
                                            Dettagli
                                        </button>
                                        <a href="{{ route('staff.withdrawals.pickup-summary', $batch->id) }}" class="px-3 py-1 bg-green-600 hover:bg-green-700 text-white text-xs font-bold rounded transition-colors inline-block">
                                            Riepilogo
                                        </a>
                                    </td>
                                </tr>
                                <tr id="pickup-details-{{ $batch->id }}" class="hidden">
                                    <td colspan="3" class="px-6 py-4">
                                        <div class="bg-gray-50 rounded-lg p-4">
                                            <div class="overflow-x-auto">
                                                <table class="w-full text-xs">
                                                    <thead class="bg-gray-200">
                                                        <tr>
                                                            <th class="px-3 py-2 text-left font-semibold text-gray-900">#</th>
                                                            <th class="px-3 py-2 text-left font-semibold text-gray-900">Libro</th>
                                                            <th class="px-3 py-2 text-left font-semibold text-gray-900">ISBN</th>
                                                            <th class="px-3 py-2 text-center font-semibold text-gray-900">Stato</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody class="divide-y divide-gray-300">
                                                        @foreach($batch->pickups as $index => $pickup)
                                                            <tr class="hover:bg-gray-100">
                                                                <td class="px-3 py-2 text-gray-900">{{ $index + 1 }}</td>
                                                                <td class="px-3 py-2 text-gray-900 font-medium">{{ $pickup->bookListing->book->title }}</td>
                                                                <td class="px-3 py-2 text-gray-600">{{ $pickup->bookListing->book->isbn ?? '-' }}</td>
                                                                <td class="px-3 py-2 text-center">
                                                                    <span class="px-2 py-1 rounded text-white text-xs font-semibold @if($pickup->leave) bg-gray-600 @else bg-red-600 @endif">
                                                                        @if($pickup->leave)
                                                                            Archiviato
                                                                        @else
                                                                            Ritirato
                                                                        @endif
                                                                    </span>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="p-8 text-center text-gray-500">
                    <p class="text-lg">Nessun pickup registrato</p>
                </div>
            @endif
        </div>

        <script>
            function toggleDetails(batchId) {
                const detailsRow = document.getElementById('details-' + batchId);
                if (detailsRow.classList.contains('hidden')) {
                    detailsRow.classList.remove('hidden');
                } else {
                    detailsRow.classList.add('hidden');
                }
            }

            function togglePickupDetails(batchId) {
                const detailsRow = document.getElementById('pickup-details-' + batchId);
                if (detailsRow.classList.contains('hidden')) {
                    detailsRow.classList.remove('hidden');
                } else {
                    detailsRow.classList.add('hidden');
                }
            }
        </script>

    </div>
</div>
@endsection
