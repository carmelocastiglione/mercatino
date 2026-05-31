@extends('layouts.app-student')

@section('title', 'Segnala Problema')

@section('content')
    <div class="mb-8">
        <a href="javascript:history.back()" class="text-blue-600 hover:text-blue-800 font-medium">← Torna indietro</a>
        <h1 class="text-4xl font-bold text-gray-900 mt-4">Segnala un Problema</h1>
        <p class="text-gray-600 mt-2">Aiutaci a migliorare segnalando i problemi che riscontri sulla piattaforma</p>
    </div>

    @if($errors->any())
        <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
            <h3 class="text-red-800 font-medium mb-2">Errori di validazione:</h3>
            <ul class="text-red-700 text-sm space-y-1">
                @foreach($errors->all() as $error)
                    <li>• {{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(session('success'))
        <div class="mb-6 bg-green-50 border border-green-200 rounded-lg p-4">
            <p class="text-green-800 font-medium">{{ session('success') }}</p>
        </div>
    @endif

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-8 max-w-2xl">
        <form action="{{ route('student.problems.store') }}" method="POST">
            @csrf

            <div>
                <label for="description" class="block text-sm font-semibold text-gray-900 mb-3">
                    Descrivi il problema
                </label>
                <textarea
                    id="description"
                    name="description"
                    rows="10"
                    placeholder="Spiega nel dettaglio il problema che hai riscontrato. Includi:&#10;- Dove è accaduto (pagina, sezione, ecc.)&#10;- Cosa hai fatto&#10;- Cosa ti aspettavi&#10;- Cosa è successo invece"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none @error('description') border-red-500 @enderror"
                    required
                >{{ old('description') }}</textarea>
                @error('description')
                    <p class="text-red-600 text-sm mt-2">{{ $message }}</p>
                @enderror
                <p class="text-gray-600 text-sm mt-2">Minimo 10 caratteri, massimo 2000</p>
            </div>

            <div class="mt-8 flex gap-3">
                <button
                    type="submit"
                    class="px-6 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition"
                >
                    Invia Segnalazione
                </button>
                <button
                    type="button"
                    onclick="history.back()"
                    class="px-6 py-3 text-gray-700 font-medium rounded-lg hover:bg-gray-100 transition"
                >
                    Annulla
                </button>
            </div>
        </form>
    </div>

    <div class="mt-8 bg-blue-50 border border-blue-200 rounded-lg p-6 max-w-2xl">
        <h3 class="text-blue-900 font-semibold mb-3">💡 Consigli per una buona segnalazione:</h3>
        <ul class="text-blue-800 text-sm space-y-2">
            <li>✓ Sii specifico e dettagliato</li>
            <li>✓ Descrivi i passaggi esatti per riprodurre il problema</li>
            <li>✓ Includi messaggi di errore se presenti</li>
            <li>✓ Una segnalazione chiara aiuta a risolvere il problema più velocemente</li>
        </ul>
    </div>
@endsection
