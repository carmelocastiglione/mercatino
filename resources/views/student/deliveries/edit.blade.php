@extends('layouts.app-student')

@section('title', 'Modifica Consegna')

@section('content')
    <div class="mb-8">
        <a href="{{ route('student.deliveries.index') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium mb-4 inline-block">
            ← Torna alle consegne
        </a>
        <h1 class="text-4xl font-bold text-gray-900">Modifica Consegna</h1>
        <p class="text-gray-600 mt-2">Modifica i dettagli della tua consegna</p>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-8 max-w-2xl">
        <form action="{{ route('student.deliveries.update', $delivery) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Libro -->
            <div>
                <label for="book_id" class="block text-sm font-medium text-gray-900 mb-2">
                    Seleziona il libro <span class="text-red-600">*</span>
                </label>
                <select name="book_id" id="book_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('book_id') border-red-500 @enderror">
                    <option value="">-- Scegli un libro --</option>
                    @foreach($books as $book)
                        <option value="{{ $book->id }}" data-price="{{ $book->original_price }}" @selected(old('book_id', $delivery->book_id) == $book->id)>
                            {{ $book->title }} - {{ $book->author }}
                        </option>
                    @endforeach
                </select>
                @error('book_id')
                    <p class="text-red-600 text-sm mt-2">{{ $message }}</p>
                @enderror
            </div>

            <!-- Condizioni -->
            <div>
                <label for="condition" class="block text-sm font-medium text-gray-900 mb-2">
                    Condizioni del libro <span class="text-red-600">*</span>
                </label>
                <select name="condition" id="condition" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('condition') border-red-500 @enderror">
                    <option value="">-- Scegli le condizioni --</option>
                    <option value="like-new" @selected(old('condition', $delivery->condition) == 'like-new')>Come nuovo</option>
                    <option value="good" @selected(old('condition', $delivery->condition) == 'good')>Buono</option>
                    <option value="fair" @selected(old('condition', $delivery->condition) == 'fair')>Accettabile</option>
                    <option value="poor" @selected(old('condition', $delivery->condition) == 'poor')>Rovinato</option>
                </select>
                @error('condition')
                    <p class="text-red-600 text-sm mt-2">{{ $message }}</p>
                @enderror
            </div>

            <!-- Prezzo Calcolato -->
            <div>
                <label for="calculated_price" class="block text-sm font-medium text-gray-900 mb-2">
                    Prezzo proposto (€)
                </label>
                <input type="text" id="calculated_price" class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50 focus:outline-none cursor-not-allowed" readonly placeholder="Seleziona un libro per vedere il prezzo">
                <p class="text-gray-500 text-sm mt-2">Il prezzo è calcolato automaticamente come metà del prezzo originale del libro (arrotondato per difetto)</p>
            </div>

            <!-- Info -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <p class="text-sm text-blue-900">
                    <strong>Nota:</strong> Puoi modificare questa consegna solo finché è in sospeso. Una volta approvata dallo staff, non potrai più apportare modifiche.
                </p>
            </div>

            <!-- Buttons -->
            <div class="flex gap-4 pt-4">
                <button type="submit" class="flex-1 bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition font-medium">
                    Salva Modifiche
                </button>
                <a href="{{ route('student.deliveries.index') }}" class="flex-1 bg-gray-200 text-gray-900 px-6 py-3 rounded-lg hover:bg-gray-300 transition font-medium text-center">
                    Annulla
                </a>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const bookSelect = document.getElementById('book_id');
            const priceInput = document.getElementById('calculated_price');

            function calculatePrice() {
                const selectedOption = bookSelect.options[bookSelect.selectedIndex];
                const originalPrice = selectedOption.dataset.price;

                if (originalPrice) {
                    // Calcola la metà del prezzo, arrotondato per difetto all'intero
                    const calculatedPrice = Math.floor(originalPrice / 2);
                    priceInput.value = '€ ' + calculatedPrice;
                } else {
                    priceInput.value = '';
                }
            }

            bookSelect.addEventListener('change', calculatePrice);
            
            // Se c'è una selezione mantenuta dopo validazione, calcola il prezzo
            if (bookSelect.value) {
                calculatePrice();
            }
        });
    </script>
@endsection
