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

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-8 max-w-2xl mx-auto">
        <form action="{{ route('student.deliveries.update', $delivery) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Libro -->
            <div>
                <label for="book_search" class="block text-sm font-medium text-gray-900 mb-2">
                    Seleziona il libro <span class="text-red-600">*</span>
                </label>
                <div class="relative">
                    <input 
                        type="text" 
                        id="book_search" 
                        placeholder="Cerca per ISBN, titolo o autore..." 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('book_id') border-red-500 @enderror"
                        autocomplete="off"
                        value="{{ old('book_id', $delivery->book_id) ? ($delivery->book->title . ' - ' . $delivery->book->author) : '' }}"
                    />
                    <input type="hidden" id="book_id" name="book_id" value="{{ old('book_id', $delivery->book_id) }}">
                    
                    <!-- Dropdown dei risultati -->
                    <div id="search_results" class="absolute top-full left-0 right-0 bg-white border border-gray-300 rounded-lg shadow-lg mt-2 max-h-64 overflow-y-auto hidden z-10"></div>
                </div>
                <p id="selected_book" class="text-sm text-gray-600 mt-2 {{ old('book_id', $delivery->book_id) ? '' : 'hidden' }}">
                    <span class="font-medium">Libro selezionato:</span> <span id="selected_book_text">{{ old('book_id', $delivery->book_id) ? ($delivery->book->title . ' - ' . $delivery->book->author) : '' }}</span>
                </p>
                @error('book_id')
                    <p class="text-red-600 text-sm mt-2">{{ $message }}</p>
                @enderror
            </div>

            <!-- Condizioni -->
            <div>
                <label class="block text-sm font-medium text-gray-900 mb-2">
                    Condizioni del libro <span class="text-red-600">*</span>
                </label>
                <div class="grid grid-cols-2 gap-4">
                    @foreach(['like-new' => 'Come Nuovo', 'good' => 'Buona', 'fair' => 'Discreta', 'poor' => 'Scarsa'] as $value => $label)
                        <label class="flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-blue-500 transition @if($errors->has('condition')) border-red-500 @endif">
                            <input type="radio" id="condition_{{ $value }}" name="condition" value="{{ $value }}" class="w-4 h-4 text-blue-600" @checked(old('condition', $delivery->condition) == $value)>
                            <span class="ml-3 text-sm font-medium text-gray-900">{{ $label }}</span>
                        </label>
                    @endforeach
                </div>
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
        let debounceTimer;
        const bookSearch = document.getElementById('book_search');
        const bookIdInput = document.getElementById('book_id');
        const searchResults = document.getElementById('search_results');
        const selectedBookDisplay = document.getElementById('selected_book');
        const selectedBookText = document.getElementById('selected_book_text');
        const priceInput = document.getElementById('calculated_price');
        let currentBook = null;

        // Search functionality
        bookSearch.addEventListener('input', (e) => {
            clearTimeout(debounceTimer);
            const query = e.target.value.trim();
            
            if (query.length < 2) {
                searchResults.classList.add('hidden');
                return;
            }

            debounceTimer = setTimeout(() => {
                fetch(`{{ route('student.deliveries.search-books') }}?q=${encodeURIComponent(query)}`)
                    .then(response => response.json())
                    .then(books => {
                        if (books.length === 0) {
                            searchResults.innerHTML = '<div class="p-4 text-gray-500">Nessun libro trovato</div>';
                            searchResults.classList.remove('hidden');
                            return;
                        }

                        searchResults.innerHTML = books.map(book => `
                            <div class="p-4 border-b border-gray-200 hover:bg-gray-50 cursor-pointer transition" onclick="selectBook(${book.id}, '${book.title.replace(/'/g, "\\'")}', '${(book.author || '').replace(/'/g, "\\'")}', ${book.original_price})">
                                <div>
                                    <p class="font-medium text-gray-900">${book.title}</p>
                                    <p class="text-sm text-gray-600">${book.author || 'Autore sconosciuto'}</p>
                                    <p class="text-xs text-gray-500 mt-1">ISBN: ${book.isbn || 'N/A'}</p>
                                </div>
                            </div>
                        `).join('');
                        searchResults.classList.remove('hidden');
                    })
                    .catch(error => {
                        console.error('Errore nella ricerca:', error);
                        searchResults.innerHTML = '<div class="p-4 text-red-500">Errore nella ricerca</div>';
                        searchResults.classList.remove('hidden');
                    });
            }, 300);
        });

        function selectBook(id, title, author, originalPrice) {
            bookIdInput.value = id;
            bookSearch.value = title + (author ? ` - ${author}` : '');
            currentBook = { id, title, author, originalPrice };
            
            selectedBookText.textContent = `${title}${author ? ` - ${author}` : ''}`;
            selectedBookDisplay.classList.remove('hidden');
            searchResults.classList.add('hidden');
            
            calculatePrice(originalPrice);
        }

        function calculatePrice(originalPrice) {
            if (originalPrice) {
                // Calcola la metà del prezzo, arrotondato per difetto all'intero
                const calculatedPrice = Math.floor(originalPrice / 2);
                priceInput.value = '€ ' + calculatedPrice;
            } else {
                priceInput.value = '';
            }
        }

        // Se c'è una selezione mantenuta dopo validazione, ricerca e mostra il libro
        document.addEventListener('DOMContentLoaded', function() {
            if (bookIdInput.value) {
                const selectedTitle = bookSearch.value;
                const selectedId = bookIdInput.value;
                
                // Se il libro è già caricato (da modifica di una consegna esistente)
                // Mostra il titolo e calcola il prezzo cercando il libro nel DB
                if (selectedTitle) {
                    fetch(`{{ route('student.deliveries.search-books') }}?q=${encodeURIComponent(selectedTitle.substring(0, 20))}`)
                        .then(response => response.json())
                        .then(books => {
                            const book = books.find(b => b.id == selectedId);
                            if (book) {
                                calculatePrice(book.original_price);
                            }
                        })
                        .catch(error => console.error('Errore:', error));
                }
            }
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', (e) => {
            if (e.target !== bookSearch && !searchResults.contains(e.target)) {
                searchResults.classList.add('hidden');
            }
        });
    </script>
@endsection
