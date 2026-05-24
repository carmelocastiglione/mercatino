@extends('layouts.app-student')

@section('title', 'Le Mie Vendite')

@section('content')
    <div class="mb-12">
        <h1 class="text-4xl font-bold text-gray-900 mb-2">Le Mie Vendite</h1>
        <p class="text-gray-600">Storico di tutte le vendite effettuate</p>
    </div>

    <!-- Summary Stats -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-12">
        <x-stats-card label="Totale Vendite" :value="$totalSales" color="blue" />
        <x-stats-card label="Totale Guadagnato" :value="$totalEarnings" color="blue" formatted />
    </div>

    <!-- Sales Table -->
    @if($sales->count() > 0)
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Libro</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Acquirente</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Prezzo</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Data Vendita</th>
                        <th class="px-6 py-3 text-center text-sm font-semibold text-gray-900">Azioni</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($sales as $listing)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4">
                                <div>
                                    <p class="font-medium text-gray-900">{{ $listing->book->title }}</p>
                                    <p class="text-sm text-gray-600">{{ $listing->book->author }}</p>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $sale = $listing->bookSales->first();
                                @endphp
                                @if($sale && $sale->buyer)
                                    <p class="text-gray-900">{{ $sale->buyer->name }} {{ $sale->buyer->surname }}</p>
                                @else
                                    <p class="text-gray-500">-</p>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <p class="font-bold text-green-600">€ {{ number_format($listing->price, 2, ',', '.') }}</p>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ $listing->updated_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <a href="{{ route('student.sales.show', $listing->id) }}" class="text-indigo-600 hover:text-indigo-900 font-semibold text-sm">
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
            {{ $sales->links() }}
        </div>
    @else
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-12 text-center">
            <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <p class="text-gray-600 text-lg mb-6">Non hai ancora effettuato vendite</p>
            <a href="{{ route('student.dashboard') }}" class="bg-indigo-600 text-white px-6 py-3 rounded-lg hover:bg-indigo-700 transition font-medium inline-block">
                Torna alla dashboard
            </a>
        </div>
    @endif
@endsection
