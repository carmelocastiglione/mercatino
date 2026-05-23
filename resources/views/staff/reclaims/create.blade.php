@extends('layouts.app-staff')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <a href="{{ route('staff.reclaims.index') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">← Torna alla ricerca</a>
        <h1 class="text-4xl font-bold text-gray-900 mt-4">{{ $title }}</h1>
    </div>

    @if ($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded mb-6">
            <p class="font-bold">Errori:</p>
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Dettagli Libro -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">Dettagli Libro</h2>

                <div class="space-y-4">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Titolo</p>
                        <p class="text-lg font-bold text-gray-900">{{ $bookListing->book->title }}</p>
                    </div>

                    <div>
                        <p class="text-sm font-medium text-gray-500">Autore</p>
                        <p class="text-gray-700">{{ $bookListing->book->author }}</p>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Condizione</p>
                            <p class="text-gray-700 capitalize">{{ $bookListing->condition }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Prezzo</p>
                            <p class="text-gray-700">€{{ $bookListing->price }}</p>
                        </div>
                    </div>

                    <div>
                        <p class="text-sm font-medium text-gray-500">Venditore</p>
                        <p class="text-gray-700">{{ $bookListing->seller->name }} {{ $bookListing->seller->surname }}</p>
                        <p class="text-xs text-gray-500">{{ $bookListing->seller->email }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Reso -->
        <div>
            <form action="{{ route('staff.reclaims.store') }}" method="POST" class="space-y-4">
                @csrf

                <input type="hidden" name="book_listing_id" value="{{ $bookListing->id }}">

                <!-- Motivo Rifiuto -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Motivo Rifiuto</h2>
                    
                    <div>
                        <label for="rejection_reason" class="block text-sm font-medium text-gray-700 mb-2">
                            Inserisci motivo (opzionale, solo se rifiuti)
                        </label>
                        <textarea 
                            id="rejection_reason" 
                            name="rejection_reason" 
                            rows="3" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm"
                            placeholder="Es: Libro danneggiato, non corrisponde alla descrizione..."
                        ></textarea>
                    </div>
                </div>

                <!-- Azioni -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 space-y-3">
                    <button 
                        type="submit" 
                        name="action"
                        value="approve"
                        class="w-full px-4 py-3 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition"
                    >
                        ✓ Approva Reso
                    </button>

                    <button 
                        type="submit" 
                        name="action"
                        value="reject"
                        class="w-full px-4 py-3 bg-red-600 text-white font-medium rounded-lg hover:bg-red-700 transition"
                    >
                        ✕ Rifiuta Reso
                    </button>

                    <a 
                        href="{{ route('staff.reclaims.index') }}" 
                        class="block w-full text-center px-4 py-3 bg-gray-200 text-gray-700 font-medium rounded-lg hover:bg-gray-300 transition"
                    >
                        Annulla
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
