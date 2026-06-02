@extends('layouts.app-staff')

@section('title', 'Rifiuta Reso')

@section('content')
    <div class="mb-8">
        <a href="{{ route('staff.reclaims.show', $reclaim) }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium mb-4 inline-block">
            ← Torna al dettaglio
        </a>
        <h1 class="text-4xl font-bold text-gray-900">Rifiuta Reso</h1>
        <p class="text-gray-600 mt-2">Specifica il motivo del rifiuto</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <!-- Form -->
        <div class="md:col-span-2">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                    <p class="text-sm text-red-900">
                        <strong>Attenzione:</strong> Stai per rifiutare il reso del libro "{{ $reclaim->bookListing->book->title }}" da parte di {{ $reclaim->buyer->name }}.
                    </p>
                </div>

                <form action="{{ route('staff.reclaims.reject', $reclaim) }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <!-- Motivo Rifiuto -->
                    <div>
                        <label for="rejection_reason" class="block text-sm font-medium text-gray-900 mb-2">
                            Motivo del Rifiuto <span class="text-red-600">*</span>
                        </label>
                        <textarea name="rejection_reason" id="rejection_reason" rows="6" placeholder="Spiega perché stai rifiutando questo reso (es: il libro è ancora in buone condizioni, non è stato conforme alle regole, ecc.)" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 @error('rejection_reason') border-red-500 @enderror">{{ old('rejection_reason') }}</textarea>
                        @error('rejection_reason')
                            <p class="text-red-600 text-sm mt-2">{{ $message }}</p>
                        @enderror
                        <p class="text-gray-500 text-xs mt-2">Questo messaggio sarà visibile al compratore e al venditore</p>
                    </div>

                    <!-- Buttons -->
                    <div class="flex gap-4 pt-4">
                        <button type="submit" class="flex-1 bg-red-600 text-white px-6 py-3 rounded-lg hover:bg-red-700 transition font-medium">
                            Rifiuta Reso
                        </button>
                        <a href="{{ route('staff.reclaims.show', $reclaim) }}" class="flex-1 bg-gray-200 text-gray-900 px-6 py-3 rounded-lg hover:bg-gray-300 transition font-medium text-center">
                            Annulla
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Dettagli Reso Sidebar -->
        <div>
            <div class="bg-gray-50 rounded-lg shadow-sm border border-gray-200 p-6 sticky top-8">
                <h3 class="text-sm font-semibold text-gray-900 mb-4">Dettagli Reso</h3>
                <div class="space-y-3 text-sm">
                    <div>
                        <p class="text-gray-600">Compratore</p>
                        <p class="font-medium">{{ $reclaim->buyer->name }}</p>
                    </div>
                    <div>
                        <p class="text-gray-600">Venditore</p>
                        <p class="font-medium">{{ $reclaim->user->name }}</p>
                    </div>
                    <div>
                        <p class="text-gray-600">Libro</p>
                        <p class="font-medium">{{ $reclaim->bookListing->book->title }}</p>
                    </div>
                    <div>
                        <p class="text-gray-600">Condizioni</p>
                        <p class="font-medium">{{ ucfirst(str_replace('-', ' ', $reclaim->bookListing->condition)) }}</p>
                    </div>
                    <div>
                        <p class="text-gray-600">Prezzo Vendita</p>
                        <p class="font-medium">€ {{ number_format($reclaim->bookListing->price_sell, 2, ',', '.') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
