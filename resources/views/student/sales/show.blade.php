@extends('layouts.app-student')

@section('title', 'Dettagli Vendita')

@section('content')
    <div class="mb-8">
        <a href="{{ route('student.sales.index') }}" class="text-indigo-600 hover:text-indigo-900 font-semibold mb-4 inline-block">
            ← Torna alle vendite
        </a>
        <h1 class="text-4xl font-bold text-gray-900 mb-2">Dettagli Vendita</h1>
    </div>

    <!-- Sale Details Card -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Book Info -->
        <div class="bg-white rounded-xl shadow-md p-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Informazioni Libro</h2>
            
            <div class="space-y-4">
                <div>
                    <p class="text-sm text-gray-600 font-semibold">Titolo</p>
                    <p class="text-gray-900 text-lg">{{ $listing->book->title }}</p>
                </div>

                <div>
                    <p class="text-sm text-gray-600 font-semibold">Autore</p>
                    <p class="text-gray-900">{{ $listing->book->author }}</p>
                </div>

                <div>
                    <p class="text-sm text-gray-600 font-semibold">ISBN</p>
                    <p class="text-gray-900">{{ $listing->book->isbn ?? '-' }}</p>
                </div>

                <div>
                    <p class="text-sm text-gray-600 font-semibold">Condizione</p>
                    <p class="text-gray-900">{{ ucfirst($listing->condition) }}</p>
                </div>

                <div>
                    <p class="text-sm text-gray-600 font-semibold">Materia</p>
                    <p class="text-gray-900">{{ $listing->book->subject ?? '-' }}</p>
                </div>
            </div>
        </div>

        <!-- Sale Info -->
        <div class="bg-white rounded-xl shadow-md p-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Informazioni Vendita</h2>
            
            <div class="space-y-4">
                <div>
                    <p class="text-sm text-gray-600 font-semibold">Acquirente</p>
                    @php
                        $sale = $listing->bookSales->first();
                    @endphp
                    @if($sale && $sale->buyer)
                        <p class="text-gray-900 text-lg">{{ $sale->buyer->name }} {{ $sale->buyer->surname }}</p>
                        <p class="text-sm text-gray-600">{{ $sale->buyer->code ?? '-' }}</p>
                    @else
                        <p class="text-gray-500">Informazioni non disponibili</p>
                    @endif
                </div>

                <div class="pt-4 border-t border-gray-200">
                    <p class="text-sm text-gray-600 font-semibold">Prezzo di Vendita</p>
                    <p class="text-3xl font-bold text-green-600">{{ number_format($listing->price, 2, ',', '.') }}€</p>
                </div>

                <div>
                    <p class="text-sm text-gray-600 font-semibold">Data Vendita</p>
                    <p class="text-gray-900">{{ $listing->updated_at->format('d/m/Y H:i') }}</p>
                </div>

                <div>
                    <p class="text-sm text-gray-600 font-semibold">ID Listing</p>
                    <p class="text-sm text-gray-500 font-mono">{{ $listing->id }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Back Button -->
    <div class="mt-8">
        <a href="{{ route('student.sales.index') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-6 rounded-lg transition-colors">
            Torna alle vendite
        </a>
    </div>
@endsection
