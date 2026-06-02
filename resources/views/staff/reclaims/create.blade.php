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
            <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-lg shadow-md border border-blue-200 p-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-8 flex items-center gap-2">
                    Dettagli Libro
                </h2>

                <div class="space-y-6">
                    <!-- Titolo e Autore -->
                    <div class="bg-white rounded-lg p-4 border border-blue-100">
                        <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1">Titolo</p>
                        <p class="text-xl font-bold text-gray-900">{{ $bookListing->book->title }}</p>
                        <p class="text-sm text-gray-600 mt-2">{{ $bookListing->book->author }}</p>
                    </div>

                    <!-- ISBN -->
                    <div class="bg-white rounded-lg p-4 border border-blue-100">
                        <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide mb-2">ISBN</p>
                        <p class="text-gray-800 font-mono text-lg">{{ $bookListing->book->isbn ?? '—' }}</p>
                    </div>

                    <!-- Condizione e Prezzo -->
                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-white rounded-lg p-4 border border-blue-100">
                            <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide mb-2">Condizione</p>
                            @php
                                $conditions = [
                                    'like-new' => ['Come Nuovo', 'bg-green-100', 'text-green-800'],
                                    'good' => ['Buono', 'bg-blue-100', 'text-blue-800'],
                                    'fair' => ['Discreto', 'bg-yellow-100', 'text-yellow-800'],
                                    'poor' => ['Scadente', 'bg-red-100', 'text-red-800']
                                ];
                                $condData = $conditions[$bookListing->condition] ?? ['Sconosciuto', 'bg-gray-100', 'text-gray-800'];
                            @endphp
                            <span class="inline-block px-3 py-1 text-sm font-semibold rounded-full {{ $condData[1] }} {{ $condData[2] }}">
                                {{ $condData[0] }}
                            </span>
                        </div>
                        <div class="bg-white rounded-lg p-4 border border-blue-100">
                            <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide mb-2">Prezzo Vendita</p>
                            <p class="text-2xl font-bold text-green-600">€{{ number_format($bookListing->price_sell, 2, ',', '.') }}</p>
                        </div>
                    </div>

                    <!-- Venditore -->
                    <div class="bg-white rounded-lg p-4 border border-blue-100">
                        <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide mb-3">Venditore</p>
                        <div class="space-y-2">
                            <p class="text-lg font-bold text-gray-900">{{ $bookListing->seller->name }} {{ $bookListing->seller->surname }}</p>
                            <p class="text-sm text-gray-600">{{ $bookListing->seller->email }}</p>
                            <p class="text-sm text-gray-600">Codice: <span class="font-mono font-semibold text-gray-900">{{ $bookListing->seller->code }}</span></p>
                        </div>
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
                        Approva Reso
                    </button>

                    <button 
                        type="submit" 
                        name="action"
                        value="reject"
                        class="w-full px-4 py-3 bg-red-600 text-white font-medium rounded-lg hover:bg-red-700 transition"
                    >
                        Rifiuta Reso
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
