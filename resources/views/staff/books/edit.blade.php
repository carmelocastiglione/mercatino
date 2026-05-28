@extends('layouts.app-staff')

@section('title', 'Modifica Libro')

@section('content')
    <div class="mb-8">
        <h1 class="text-4xl font-bold text-gray-900">Modifica Libro</h1>
        <p class="text-gray-600 mt-2">{{ $book->title }}</p>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8 max-w-3xl">
        <form action="{{ route('staff.books.update', $book) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Title -->
            <div>
                <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                    Titolo <span class="text-red-600">*</span>
                </label>
                <input 
                    type="text" 
                    id="title" 
                    name="title" 
                    value="{{ old('title', $book->title) }}"
                    class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('title') border-red-500 @enderror"
                    required
                >
                @error('title')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Author -->
            <div>
                <label for="author" class="block text-sm font-medium text-gray-700 mb-2">
                    Autore
                </label>
                <input 
                    type="text" 
                    id="author" 
                    name="author" 
                    value="{{ old('author', $book->author) }}"
                    class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('author') border-red-500 @enderror"
                >
                @error('author')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- ISBN -->
            <div>
                <label for="isbn" class="block text-sm font-medium text-gray-700 mb-2">
                    ISBN
                </label>
                <input 
                    type="text" 
                    id="isbn" 
                    name="isbn" 
                    value="{{ old('isbn', $book->isbn) }}"
                    class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('isbn') border-red-500 @enderror"
                >
                @error('isbn')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Subject & Class (2 columns) -->
            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label for="subject" class="block text-sm font-medium text-gray-700 mb-2">
                        Materia
                    </label>
                    <input 
                        type="text" 
                        id="subject" 
                        name="subject" 
                        value="{{ old('subject', $book->subject) }}"
                        class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                </div>

                <div>
                    <label for="school_class" class="block text-sm font-medium text-gray-700 mb-2">
                        Classe
                    </label>
                    <input 
                        type="text" 
                        id="school_class" 
                        name="school_class" 
                        value="{{ old('school_class', $book->school_class) }}"
                        class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                </div>
            </div>

            <!-- Original Price -->
            <div>
                <label for="original_price" class="block text-sm font-medium text-gray-700 mb-2">
                    Prezzo di copertina (€)
                </label>
                <input 
                    type="number" 
                    id="original_price" 
                    name="original_price" 
                    value="{{ old('original_price', $book->original_price) }}"
                    step="0.01"
                    min="0"
                    class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('original_price') border-red-500 @enderror"
                >
                @error('original_price')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Description -->
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                    Descrizione
                </label>
                <textarea 
                    id="description" 
                    name="description" 
                    rows="4"
                    class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('description') border-red-500 @enderror"
                >{{ old('description', $book->description) }}</textarea>
                @error('description')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Buttons -->
            <div class="flex gap-4 pt-6">
                <button 
                    type="submit" 
                    class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition"
                >
                    Salva modifiche
                </button>
                <a 
                    href="{{ route('staff.books.index') }}" 
                    class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-6 py-2 rounded-lg font-medium transition"
                >
                    Annulla
                </a>
            </div>
        </form>
    </div>
@endsection
