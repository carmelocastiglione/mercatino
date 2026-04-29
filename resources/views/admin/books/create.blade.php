@extends('layouts.app-dashboard')

@section('dashboard-content')
    <!-- Header -->
    <div class="mb-8">
        <a href="{{ route('admin.books.index') }}" class="text-blue-600 hover:text-blue-900 font-medium text-sm mb-4 inline-block">← Torna alla lista</a>
        <h1 class="text-4xl font-bold text-gray-900 mb-2">Nuovo Libro</h1>
        <p class="text-gray-600">Aggiungi un nuovo libro al catalogo</p>
    </div>

    <!-- Form -->
    <div class="max-w-3xl bg-white rounded-lg shadow p-8">
        <form method="POST" action="{{ route('admin.books.store') }}" class="space-y-6">
            @csrf

            <!-- Title -->
            <div>
                <label for="title" class="block text-sm font-medium text-gray-900 mb-2">Titolo *</label>
                <input 
                    type="text" 
                    name="title" 
                    id="title" 
                    value="{{ old('title') }}"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition @error('title') border-red-500 @enderror"
                    placeholder="es. Matematica Avanzata"
                    required
                >
                @error('title')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Author -->
            <div>
                <label for="author" class="block text-sm font-medium text-gray-900 mb-2">Autore</label>
                <input 
                    type="text" 
                    name="author" 
                    id="author" 
                    value="{{ old('author') }}"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition"
                    placeholder="es. Paolo Rossi"
                >
            </div>

            <!-- ISBN -->
            <div>
                <label for="isbn" class="block text-sm font-medium text-gray-900 mb-2">ISBN</label>
                <input 
                    type="text" 
                    name="isbn" 
                    id="isbn" 
                    value="{{ old('isbn') }}"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition @error('isbn') border-red-500 @enderror"
                    placeholder="es. 978-3-16-148410-0"
                >
                @error('isbn')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Subject and Class -->
            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label for="subject" class="block text-sm font-medium text-gray-900 mb-2">Materia</label>
                    <input 
                        type="text" 
                        name="subject" 
                        id="subject" 
                        value="{{ old('subject') }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition @error('subject') border-red-500 @enderror"
                        placeholder="es. Matematica"
                        required
                    >
                    @error('subject')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="school_class" class="block text-sm font-medium text-gray-900 mb-2">Classe</label>
                    <input 
                        type="text" 
                        name="school_class" 
                        id="school_class" 
                        value="{{ old('school_class') }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition @error('school_class') border-red-500 @enderror"
                        placeholder="es. 3ª Liceo"
                        required
                    >
                    @error('school_class')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Description -->
            <div>
                <label for="description" class="block text-sm font-medium text-gray-900 mb-2">Descrizione</label>
                <textarea 
                    name="description" 
                    id="description" 
                    rows="4"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition"
                    placeholder="Descrivi il libro..."
                >{{ old('description') }}</textarea>
            </div>

            <!-- Original Price -->
            <div>
                <label for="original_price" class="block text-sm font-medium text-gray-900 mb-2">Prezzo Copertina (€)</label>
                <input 
                    type="number" 
                    name="original_price" 
                    id="original_price" 
                    value="{{ old('original_price') }}"
                    step="0.01"
                    min="0"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition"
                    placeholder="es. 40.00"
                >
            </div>

            <!-- Cover Image -->
            <div>
                <label for="cover_image" class="block text-sm font-medium text-gray-900 mb-2">Immagine Copertina</label>
                <input 
                    type="text" 
                    name="cover_image" 
                    id="cover_image" 
                    value="{{ old('cover_image') }}"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition"
                    placeholder="es. https://..."
                >
            </div>

            <!-- Buttons -->
            <div class="flex gap-4 pt-6 border-t border-gray-200">
                <button 
                    type="submit" 
                    class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-6 rounded-lg transition"
                >
                    Aggiungi al Catalogo
                </button>
                <a 
                    href="{{ route('admin.books.index') }}" 
                    class="bg-gray-200 hover:bg-gray-300 text-gray-900 font-medium py-2 px-6 rounded-lg transition"
                >
                    Annulla
                </a>
            </div>
        </form>
    </div>
@endsection
