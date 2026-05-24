@extends('layouts.app-student')

@section('title', 'I Miei Acquisti')

@section('content')
    <div class="mb-12">
        <h1 class="text-4xl font-bold text-gray-900 mb-2">I Miei Acquisti</h1>
        <p class="text-gray-600">Storico di tutti i libri acquistati</p>
    </div>

    <!-- Summary Stats -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-12">
        <x-stats-card label="Totale Acquisti" :value="$totalPurchases" color="purple" />
        <x-stats-card label="Totale Speso" :value="$totalSpent" color="purple" formatted />
    </div>

    <!-- Purchases Table -->
    @if($purchases->count() > 0)
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Libro</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Venditore</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Prezzo</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Data Acquisto</th>
                        <th class="px-6 py-3 text-center text-sm font-semibold text-gray-900">Azioni</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($purchases as $purchase)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4">
                                <div>
                                    <p class="font-medium text-gray-900">{{ $purchase->bookListing->book->title }}</p>
                                    <p class="text-sm text-gray-600">{{ $purchase->bookListing->book->author }}</p>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-gray-900">{{ $purchase->soldBy->name }} {{ $purchase->soldBy->surname }}</p>
                                <p class="text-sm text-gray-600">{{ $purchase->soldBy->code }}</p>
                            </td>
                            <td class="px-6 py-4 text-left">
                                <p class="font-bold text-purple-600">€ {{ number_format($purchase->bookListing->price, 2, ',', '.') }}</p>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ $purchase->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <a href="{{ route('student.purchases.show', $purchase->id) }}" class="text-purple-600 hover:text-purple-900 font-semibold text-sm">
                                    Dettagli
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-6">
            {{ $purchases->links() }}
        </div>
    @else
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-12 text-center">
            <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <p class="text-gray-600 text-lg mb-6">Non hai ancora acquistato nessun libro</p>
            <a href="{{ route('student.dashboard') }}" class="bg-purple-600 text-white px-6 py-3 rounded-lg hover:bg-purple-700 transition font-medium inline-block">
                Torna alla dashboard
            </a>
        </div>
    @endif
@endsection
