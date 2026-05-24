@extends('layouts.app-student')

@section('title', 'Dettagli Acquisto')

@section('content')
    <div class="mb-8">
        <a href="{{ route('student.purchases.index') }}" class="text-purple-600 hover:text-purple-900 font-semibold mb-4 inline-block">
            ← Torna agli acquisti
        </a>
        <h1 class="text-4xl font-bold text-gray-900 mb-2">Dettagli Acquisto</h1>
    </div>

    <!-- Purchase Details Card -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Book Info -->
        <div class="bg-white rounded-xl shadow-md p-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Informazioni Libro</h2>
            
            <div class="space-y-4">
                <div>
                    <p class="text-sm text-gray-600 font-semibold">Titolo</p>
                    <p class="text-gray-900 text-lg">{{ $purchase->bookListing->book->title }}</p>
                </div>

                <div>
                    <p class="text-sm text-gray-600 font-semibold">Autore</p>
                    <p class="text-gray-900">{{ $purchase->bookListing->book->author }}</p>
                </div>

                <div>
                    <p class="text-sm text-gray-600 font-semibold">ISBN</p>
                    <p class="text-gray-900">{{ $purchase->bookListing->book->isbn ?? '-' }}</p>
                </div>

                <div>
                    <p class="text-sm text-gray-600 font-semibold">Condizione</p>
                    <p class="text-gray-900">{{ ucfirst($purchase->bookListing->condition) }}</p>
                </div>

                <div>
                    <p class="text-sm text-gray-600 font-semibold">Materia</p>
                    <p class="text-gray-900">{{ $purchase->bookListing->book->subject ?? '-' }}</p>
                </div>
            </div>
        </div>

        <!-- Purchase Info -->
        <div class="bg-white rounded-xl shadow-md p-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Informazioni Acquisto</h2>
            
            <div class="space-y-4">
                <div>
                    <p class="text-sm text-gray-600 font-semibold">Venditore</p>
                    <p class="text-gray-900 text-lg">{{ $purchase->soldBy->name }} {{ $purchase->soldBy->surname }}</p>
                    <p class="text-sm text-gray-600">{{ $purchase->soldBy->code }}</p>
                </div>

                <div class="pt-4 border-t border-gray-200">
                    <p class="text-sm text-gray-600 font-semibold">Prezzo di Acquisto</p>
                    <p class="text-3xl font-bold text-purple-600">{{ number_format($purchase->bookListing->price, 2, ',', '.') }}€</p>
                </div>

                <div>
                    <p class="text-sm text-gray-600 font-semibold">Data Acquisto</p>
                    <p class="text-gray-900">{{ $purchase->created_at->format('d/m/Y H:i') }}</p>
                </div>

                <div>
                    <p class="text-sm text-gray-600 font-semibold">ID Acquisto</p>
                    <p class="text-sm text-gray-500 font-mono">{{ $purchase->id }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Back Button -->
    <div class="mt-8">
        <a href="{{ route('student.purchases.index') }}" class="bg-purple-600 hover:bg-purple-700 text-white font-bold py-3 px-6 rounded-lg transition-colors">
            Torna agli acquisti
        </a>
    </div>
@endsection
