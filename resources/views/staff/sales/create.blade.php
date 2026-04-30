@extends('layouts.app-staff')

@section('title', 'Registra Vendita')

@section('content')
    <div class="mb-8">
        <div class="flex items-center space-x-4 mb-6">
            <a href="{{ route('staff.sales.index') }}" class="text-green-600 hover:text-green-800 font-medium">← Torna alle vendite</a>
        </div>
        <h1 class="text-4xl font-bold text-gray-900">Registra Vendita</h1>
        <p class="text-gray-600 mt-2">Registra un libro come venduto al mercatino</p>
    </div>

    <div class="max-w-2xl">
        <form action="{{ route('staff.sales.store') }}" method="POST" class="bg-white rounded-lg shadow-sm border border-gray-200 p-8">
            @csrf

            <!-- Book Listing Selection -->
            <div class="mb-8">
                <label for="book_listing_id" class="block text-sm font-semibold text-gray-900 mb-2">
                    Libro <span class="text-red-600">*</span>
                </label>
                <select name="book_listing_id" id="book_listing_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent @error('book_listing_id') border-red-500 @enderror" required>
                    <option value="">Seleziona un libro disponibile...</option>
                    @foreach($availableListings as $listing)
                        <option value="{{ $listing->id }}" @selected(old('book_listing_id') == $listing->id)>
                            {{ $listing->book->title }} - {{ $listing->book->author }} (€{{ number_format($listing->price, 2) }})
                        </option>
                    @endforeach
                </select>
                @error('book_listing_id')
                    <p class="text-red-600 text-sm mt-2">{{ $message }}</p>
                @enderror
            </div>

            <!-- Payment Method -->
            <div class="mb-8">
                <label for="payment_method" class="block text-sm font-semibold text-gray-900 mb-2">
                    Metodo di Pagamento <span class="text-red-600">*</span>
                </label>
                <div class="grid grid-cols-2 gap-4">
                    @foreach(['cash' => 'Contanti', 'card' => 'Carta', 'bank_transfer' => 'Bonifico', 'satispay' => 'Satispay', 'paypal' => 'PayPal'] as $value => $label)
                        <label class="flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-green-500 transition @error('payment_method') border-red-500 @enderror" @checked(old('payment_method') == $value)>
                            <input type="radio" name="payment_method" value="{{ $value }}" class="w-4 h-4 text-green-600" @checked(old('payment_method') == $value) required>
                            <span class="ml-3 text-sm font-medium text-gray-900">{{ $label }}</span>
                        </label>
                    @endforeach
                </div>
                @error('payment_method')
                    <p class="text-red-600 text-sm mt-2">{{ $message }}</p>
                @enderror
            </div>

            <!-- Notes -->
            <div class="mb-8">
                <label for="notes" class="block text-sm font-semibold text-gray-900 mb-2">
                    Note (opzionale)
                </label>
                <textarea name="notes" id="notes" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent @error('notes') border-red-500 @enderror" placeholder="Aggiungi eventuali note...">{{ old('notes') }}</textarea>
                @error('notes')
                    <p class="text-red-600 text-sm mt-2">{{ $message }}</p>
                @enderror
            </div>

            <!-- Actions -->
            <div class="flex items-center space-x-4">
                <button type="submit" class="px-6 py-3 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition">
                    Registra Vendita
                </button>
                <a href="{{ route('staff.sales.index') }}" class="px-6 py-3 text-gray-700 font-medium rounded-lg hover:bg-gray-100 transition">
                    Annulla
                </a>
            </div>
        </form>
    </div>
@endsection
