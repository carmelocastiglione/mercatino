@extends('layouts.app-dashboard')

@section('dashboard-content')
    <!-- Header -->
    <div class="mb-8">
        <a href="{{ route('admin.listings.index') }}" class="text-blue-600 hover:text-blue-900 font-medium text-sm mb-4 inline-block">← Torna alla lista</a>
        <h1 class="text-4xl font-bold text-gray-900 mb-2">Modifica Annuncio</h1>
        <p class="text-gray-600">Aggiorna le informazioni dell'annuncio</p>
    </div>

    <!-- Form -->
    <div class="max-w-3xl bg-white rounded-lg shadow p-8">
        <form method="POST" action="{{ route('admin.listings.update', $listing) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Book -->
            <div>
                <label for="book_id" class="block text-sm font-medium text-gray-900 mb-2">Libro *</label>
                <select 
                    name="book_id" 
                    id="book_id"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition @error('book_id') border-red-500 @enderror"
                    required
                >
                    <option value="">Seleziona un libro dal catalogo</option>
                    @foreach ($books as $book)
                        <option value="{{ $book->id }}" @selected(old('book_id', $listing->book_id) == $book->id)>
                            {{ $book->title }} - {{ $book->subject }} ({{ $book->school_class }})
                        </option>
                    @endforeach
                </select>
                @error('book_id')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Seller -->
            <div>
                <label for="seller_id" class="block text-sm font-medium text-gray-900 mb-2">Venditore (Studente) *</label>
                <select 
                    name="seller_id" 
                    id="seller_id"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition @error('seller_id') border-red-500 @enderror"
                    required
                >
                    <option value="">Seleziona uno studente</option>
                    @foreach ($users as $user)
                        <option value="{{ $user->id }}" @selected(old('seller_id', $listing->seller_id) == $user->id)>
                            {{ $user->name }} {{ $user->surname }} ({{ $user->email }})
                        </option>
                    @endforeach
                </select>
                @error('seller_id')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Condition and Price -->
            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label for="condition" class="block text-sm font-medium text-gray-900 mb-2">Condizione *</label>
                    <select 
                        name="condition" 
                        id="condition"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition @error('condition') border-red-500 @enderror"
                        required
                    >
                        <option value="">Seleziona</option>
                        <option value="like-new" @selected(old('condition', $listing->condition) === 'like-new')>Come nuovo</option>
                        <option value="good" @selected(old('condition', $listing->condition) === 'good')>Buono</option>
                        <option value="fair" @selected(old('condition', $listing->condition) === 'fair')>Accettabile</option>
                        <option value="poor" @selected(old('condition', $listing->condition) === 'poor')>Rovinato</option>
                    </select>
                    @error('condition')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="price" class="block text-sm font-medium text-gray-900 mb-2">Prezzo (€) *</label>
                    <input 
                        type="number" 
                        name="price" 
                        id="price" 
                        value="{{ old('price', $listing->price) }}"
                        step="0.01"
                        min="0.01"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition @error('price') border-red-500 @enderror"
                        placeholder="es. 15.00"
                        required
                    >
                    @error('price')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Status -->
            <div>
                <label for="status" class="block text-sm font-medium text-gray-900 mb-2">Stato *</label>
                <select 
                    name="status" 
                    id="status"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition @error('status') border-red-500 @enderror"
                    required
                >
                    <option value="">Seleziona</option>
                    <option value="available" @selected(old('status', $listing->status) === 'available')>Disponibile</option>
                    <option value="reserved" @selected(old('status', $listing->status) === 'reserved')>Riservato</option>
                    <option value="sold" @selected(old('status', $listing->status) === 'sold')>Venduto</option>
                    <option value="archived" @selected(old('status', $listing->status) === 'archived')>Archiviato</option>
                </select>
                @error('status')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Buttons -->
            <div class="flex gap-4 pt-6 border-t border-gray-200">
                <button 
                    type="submit" 
                    class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-6 rounded-lg transition"
                >
                    Aggiorna Annuncio
                </button>
                <a 
                    href="{{ route('admin.listings.index') }}" 
                    class="bg-gray-200 hover:bg-gray-300 text-gray-900 font-medium py-2 px-6 rounded-lg transition"
                >
                    Annulla
                </a>
            </div>
        </form>
    </div>
@endsection
