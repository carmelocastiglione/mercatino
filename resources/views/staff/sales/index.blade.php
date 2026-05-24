@extends('layouts.app-staff')

@section('title', 'Vendite')

@section('content')
    <div class="mb-8">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-4xl font-bold text-gray-900">Vendite</h1>
                <p class="text-gray-600 mt-2">Libri venduti al mercatino</p>
            </div>
            <a href="{{ route('staff.sales.create') }}" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-medium">
                + Registra Vendita
            </a>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <x-stats-card label="Vendite Totali" :value="$totalSalesCount" color="green" />
            <x-stats-card label="Incasso Totale" :value="$totalRevenue" color="green" formatted />
        </div>
    </div>

    @if(session('success'))
        <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    @if($sales->count() > 0)
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Libro</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Prezzo</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Pagamento</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Venduto da</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Data</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($sales as $sale)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 text-sm text-gray-900 font-medium">
                                    <div>
                                        <p class="font-semibold">{{ $sale->bookListing->book->title }}</p>
                                        <p class="text-xs text-gray-500">{{ $sale->bookListing->book->author }}</p>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm font-semibold text-gray-900">
                                    €{{ number_format($sale->bookListing->price, 2) }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    <span class="px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-xs font-medium">
                                        @switch($sale->payment_method)
                                            @case('cash')
                                                Contanti
                                                @break
                                            @case('card')
                                                Carta
                                                @break
                                            @case('bank_transfer')
                                                Bonifico
                                                @break
                                            @case('satispay')
                                                Satispay
                                                @break
                                            @case('paypal')
                                                PayPal
                                                @break
                                            @default
                                                {{ ucfirst($sale->payment_method) }}
                                        @endswitch
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    {{ $sale->soldBy->name }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    {{ $sale->created_at->format('d/m/Y H:i') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4 border-t border-gray-200">
                {{ $sales->links() }}
            </div>
        </div>
    @else
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-12 text-center">
            <div class="text-5xl mb-4">🛍️</div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Nessuna vendita</h3>
            <p class="text-gray-600 mb-6">Nessun libro è stato ancora venduto. Inizia a registrare le vendite!</p>
            <a href="{{ route('staff.sales.create') }}" class="inline-block px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-medium">
                + Registra Prima Vendita
            </a>
        </div>
    @endif
@endsection
