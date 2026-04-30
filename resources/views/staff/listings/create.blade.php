@extends('layouts.app-staff')

@section('title', 'Acquisisci Libro')

@section('content')
    <div class="mb-8">
        <div class="flex items-center space-x-4 mb-6">
            <a href="{{ route('staff.listings.index') }}" class="text-blue-600 hover:text-blue-800 font-medium">← Torna alle acquisizioni</a>
        </div>
        <h1 class="text-4xl font-bold text-gray-900">Acquisisci Libro</h1>
        <p class="text-gray-600 mt-2">Aggiungi un nuovo libro al catalogo disponibile per la vendita</p>
    </div>

    <div class="max-w-2xl">
        <form action="{{ route('staff.listings.store') }}" method="POST" class="bg-white rounded-lg shadow-sm border border-gray-200 p-8">
            @csrf

            <!-- Book Selection -->
            <div class="mb-8">
                <label for="book_id" class="block text-sm font-semibold text-gray-900 mb-2">
                    Libro <span class="text-red-600">*</span>
                </label>
                <select name="book_id" id="book_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('book_id') border-red-500 @enderror" required>
                    <option value="">Seleziona un libro...</option>
                    @foreach($books as $book)
                        <option value="{{ $book->id }}" @selected(old('book_id') == $book->id)>
                            {{ $book->title }} - {{ $book->author }}
                        </option>
                    @endforeach
                </select>
                @error('book_id')
                    <p class="text-red-600 text-sm mt-2">{{ $message }}</p>
                @enderror
            </div>

            <!-- Condition -->
            <div class="mb-8">
                <label for="condition" class="block text-sm font-semibold text-gray-900 mb-2">
                    Condizione <span class="text-red-600">*</span>
                </label>
                <div class="grid grid-cols-2 gap-4">
                    @foreach(['like-new' => 'Come Nuovo', 'good' => 'Buona', 'fair' => 'Discreta', 'poor' => 'Scarsa'] as $value => $label)
                        <label class="flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-blue-500 transition @error('condition') border-red-500 @enderror" @checked(old('condition') == $value)>
                            <input type="radio" name="condition" value="{{ $value }}" class="w-4 h-4 text-blue-600" @checked(old('condition') == $value) required>
                            <span class="ml-3 text-sm font-medium text-gray-900">{{ $label }}</span>
                        </label>
                    @endforeach
                </div>
                @error('condition')
                    <p class="text-red-600 text-sm mt-2">{{ $message }}</p>
                @enderror
            </div>

            <!-- Price -->
            <div class="mb-8">
                <label for="price" class="block text-sm font-semibold text-gray-900 mb-2">
                    Prezzo (€) <span class="text-red-600">*</span>
                </label>
                <input type="number" name="price" id="price" step="0.01" min="0" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('price') border-red-500 @enderror" value="{{ old('price') }}" required>
                @error('price')
                    <p class="text-red-600 text-sm mt-2">{{ $message }}</p>
                @enderror
            </div>

            <!-- Actions -->
            <div class="flex items-center space-x-4">
                <button type="submit" class="px-6 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition">
                    Acquisisci Libro
                </button>
                <a href="{{ route('staff.listings.index') }}" class="px-6 py-3 text-gray-700 font-medium rounded-lg hover:bg-gray-100 transition">
                    Annulla
                </a>
            </div>
        </form>
    </div>
@endsection
