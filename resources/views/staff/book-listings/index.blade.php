@extends('layouts.app-staff')

@section('title', 'Libri Disponibili')

@section('content')
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-4xl font-bold text-gray-900 mb-2">Libri Disponibili</h1>
        <p class="text-gray-600">Visualizza tutti i libri disponibili nel mercatino</p>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <x-stats-card label="Libri Disponibili" :value="$totalAvailableBooks" color="purple" />
        <x-stats-card label="Valore di Acquisizione" :value="$totalAcquisitionAmount" color="blue" formatted />
        <x-stats-card label="Valore di Vendita" :value="$totalSalesAmount" color="green" formatted />
    </div>

    @if($listings->count() > 0)
        <!-- Filter Form -->
        <div class="bg-white border border-purple-200 rounded-lg p-6 mb-8">
            <form method="GET" action="{{ route('staff.book-listings.index') }}" class="flex gap-2">
                <input 
                    type="text" 
                    name="q" 
                    placeholder="Filtra per titolo, autore o codice ISBN..." 
                    value="{{ $filterQuery }}"
                    class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                    autocomplete="off"
                />
                <button type="submit" class="px-6 py-2 bg-purple-600 text-white font-medium rounded-lg hover:bg-purple-700 transition">
                    Filtra
                </button>
                @if($filterQuery)
                    <a href="{{ route('staff.book-listings.index') }}" class="px-6 py-2 bg-gray-500 text-white font-medium rounded-lg hover:bg-gray-600 transition">
                        Reset
                    </a>
                @endif
            </form>
            @if($filterQuery)
                <p class="text-sm text-gray-600 mt-2">Risultati per: <strong>{{ $filterQuery }}</strong> ({{ $listings->total() }} risultati)</p>
            @endif
        </div>
    @endif

    <!-- Listings Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Libro</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Venditore</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Condizione</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Prezzo Acq.</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Prezzo Vend.</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Data Acquisizione</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse ($listings as $listing)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4">
                                <p class="font-medium text-gray-900">{{ $listing->book->title }}</p>
                                <p class="text-xs text-gray-500 mt-1">{{ $listing->book->author ?? 'N/A' }}</p>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ $listing->seller->name }} {{ $listing->seller->surname }}
                                <p class="text-xs text-gray-500 mt-1">{{ $listing->seller->code }}</p>
                            </td>
                            <td class="px-6 py-4 text-sm">
                                @if ($listing->condition === 'like-new')
                                    <span class="px-3 py-1 text-xs font-semibold text-green-800 bg-green-100 rounded-full">Come nuovo</span>
                                @elseif ($listing->condition === 'good')
                                    <span class="px-3 py-1 text-xs font-semibold text-blue-800 bg-blue-100 rounded-full">Buono</span>
                                @elseif ($listing->condition === 'fair')
                                    <span class="px-3 py-1 text-xs font-semibold text-yellow-800 bg-yellow-100 rounded-full">Accettabile</span>
                                @else
                                    <span class="px-3 py-1 text-xs font-semibold text-red-800 bg-red-100 rounded-full">Rovinato</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm font-semibold text-gray-900">€ {{ number_format($listing->price, 2, ',', '.') }}</td>
                            <td class="px-6 py-4 text-sm font-semibold text-green-600">{{ $listing->price_sell ? '€ ' . number_format($listing->price_sell, 2, ',', '.') : '-' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $listing->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                <p class="text-lg">Nessun libro disponibile</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    @if ($listings->hasPages())
        <div class="mt-6">
            {{ $listings->links() }}
        </div>
    @endif
@endsection
