@extends('layouts.app-staff')

@section('title', 'Aggiungi Data di Ritiro Prenotazioni')

@section('content')
    <div class="mb-8">
        <a href="{{ route('staff.reservation-dates.index') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium mb-4 inline-block">
            ← Torna alle date di ritiro prenotazioni
        </a>
        <h1 class="text-4xl font-bold text-gray-900">Aggiungi Data di Ritiro Prenotazioni</h1>
        <p class="text-gray-600 mt-2">Configura una nuova data per il ritiro dei libri prenotati online</p>
    </div>

    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-8">
            <form action="{{ route('staff.reservation-dates.store') }}" method="POST" class="space-y-8">
                @csrf

                <!-- Data di Ritiro -->
                <div>
                    <label for="scheduled_date" class="block text-sm font-semibold text-gray-900 mb-2">
                        Data di Ritiro <span class="text-red-600">*</span>
                    </label>
                    <input 
                        type="date" 
                        id="scheduled_date" 
                        name="scheduled_date" 
                        value="{{ old('scheduled_date') }}" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        required
                        min="{{ now()->format('Y-m-d') }}"
                    />
                    @error('scheduled_date')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-sm text-gray-600 mt-2">La data deve essere nel futuro</p>
                </div>

                <!-- Etichetta (Label) -->
                <div>
                    <label for="label" class="block text-sm font-semibold text-gray-900 mb-2">
                        Etichetta <span class="text-gray-500 text-sm">(opzionale)</span>
                    </label>
                    <input 
                        type="text" 
                        id="label" 
                        name="label" 
                        value="{{ old('label') }}" 
                        placeholder="Es: Ritiro mattina, Ritiro pomeriggio..." 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        maxlength="255"
                    />
                    @error('label')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-sm text-gray-600 mt-2">Un'etichetta descrittiva per identificare questo ritiro</p>
                </div>

                <!-- Info Box -->
                <div class="bg-blue-50 border border-blue-300 rounded-lg p-4">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-blue-700">
                                <span class="font-medium">💡 Suggerimento:</span> Puoi aggiungere più date di ritiro. Gli studenti potranno sceglierne una quando prenotano il ritiro dei loro libri acquistati.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Pulsanti -->
                <div class="flex items-center gap-4 pt-4 border-t border-gray-200">
                    <button type="submit" class="px-8 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition">
                        ✓ Aggiungi Data
                    </button>
                    <a href="{{ route('staff.reservation-dates.index') }}" class="px-8 py-3 text-gray-700 font-medium rounded-lg hover:bg-gray-100 transition">
                        Annulla
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection
