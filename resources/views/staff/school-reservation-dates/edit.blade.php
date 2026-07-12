@extends('layouts.app-staff')

@section('title', 'Modifica Data di Ritiro Prenotazioni')

@section('content')
    <div class="mb-8">
        <a href="{{ route('staff.reservation-dates.index') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium mb-4 inline-block">
            ← Torna alle date di ritiro prenotazioni
        </a>
        <h1 class="text-4xl font-bold text-gray-900">Modifica Data di Ritiro Prenotazioni</h1>
        <p class="text-gray-600 mt-2">Aggiorna i dettagli di questa data di ritiro</p>
    </div>

    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-8">
            <form action="{{ route('staff.reservation-dates.update', $reservationDate) }}" method="POST" class="space-y-8">
                @csrf
                @method('PUT')

                <!-- Data di Ritiro -->
                <div>
                    <label for="scheduled_date" class="block text-sm font-semibold text-gray-900 mb-2">
                        Data di Ritiro <span class="text-red-600">*</span>
                    </label>
                    <input 
                        type="date" 
                        id="scheduled_date" 
                        name="scheduled_date" 
                        value="{{ old('scheduled_date', $reservationDate->scheduled_date->format('Y-m-d')) }}" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        required
                    />
                    @error('scheduled_date')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
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
                        value="{{ old('label', $reservationDate->label) }}" 
                        placeholder="Es: Ritiro mattina, Ritiro pomeriggio..." 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        maxlength="255"
                    />
                    @error('label')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Stato Attivo -->
                <div>
                    <label class="flex items-center">
                        <input 
                            type="checkbox" 
                            name="is_active" 
                            value="1" 
                            {{ old('is_active', $reservationDate->is_active) ? 'checked' : '' }} 
                            class="w-4 h-4 text-blue-600 rounded focus:ring-blue-500 cursor-pointer"
                        />
                        <span class="ml-3 text-sm font-medium text-gray-900">Data Attiva</span>
                    </label>
                    <p class="text-sm text-gray-600 mt-2">Se spuntata, gli studenti potranno selezionare questa data quando prenotano il ritiro dei loro libri</p>
                </div>

                <!-- Info Box -->
                <div class="bg-amber-50 border border-amber-300 rounded-lg p-4">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-amber-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-amber-700">
                                <span class="font-medium">⚠️ Attenzione:</span> Se disattivi questa data, non comparirà più nelle scelte degli studenti. Eventuali prenotazioni già effettuate per questa data rimarranno comunque valide.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Pulsanti -->
                <div class="flex items-center gap-4 pt-4 border-t border-gray-200">
                    <button type="submit" class="px-8 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition">
                        ✓ Salva Modifiche
                    </button>
                    <a href="{{ route('staff.reservation-dates.index') }}" class="px-8 py-3 text-gray-700 font-medium rounded-lg hover:bg-gray-100 transition">
                        Annulla
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection
