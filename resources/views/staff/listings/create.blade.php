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

            <!-- Seller Selection -->
            <div class="mb-8">
                <label for="seller_search" class="block text-sm font-semibold text-gray-900 mb-2">
                    Venditore <span class="text-red-600">*</span>
                </label>
                <div class="relative">
                    <input 
                        type="text" 
                        id="seller_search" 
                        placeholder="Cerca per nome o email..." 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        autocomplete="off"
                    />
                    <input type="hidden" id="seller_id" name="seller_id" value="">
                    
                    <!-- Dropdown dei risultati -->
                    <div id="seller_results" class="absolute top-full left-0 right-0 bg-white border border-gray-300 rounded-lg shadow-lg mt-2 max-h-64 overflow-y-auto hidden z-10"></div>
                </div>
                @error('seller_id')
                    <p class="text-red-600 text-sm mt-2">{{ $message }}</p>
                @enderror
                <p id="selected_seller" class="text-sm text-gray-600 mt-2 hidden">
                    <span class="font-medium">Venditore selezionato:</span> <span id="selected_seller_text"></span>
                </p>
            </div>

            <!-- Book Selection -->
            <div class="mb-8">
                <label for="book_search" class="block text-sm font-semibold text-gray-900 mb-2">
                    Libro <span class="text-red-600">*</span>
                </label>
                <div class="relative">
                    <input 
                        type="text" 
                        id="book_search" 
                        placeholder="Cerca per titolo, autore o ISBN..." 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        autocomplete="off"
                    />
                    <input type="hidden" id="book_id" name="book_id" value="">
                    
                    <!-- Dropdown dei risultati -->
                    <div id="search_results" class="absolute top-full left-0 right-0 bg-white border border-gray-300 rounded-lg shadow-lg mt-2 max-h-64 overflow-y-auto hidden z-10"></div>
                </div>
                @error('book_id')
                    <p class="text-red-600 text-sm mt-2">{{ $message }}</p>
                @enderror
                <p id="selected_book" class="text-sm text-gray-600 mt-2 hidden">
                    <span class="font-medium">Libro selezionato:</span> <span id="selected_book_text"></span>
                </p>
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

    <script>
        // === SELLER SEARCH ===
        const sellerSearch = document.getElementById('seller_search');
        const sellerResults = document.getElementById('seller_results');
        const sellerIdInput = document.getElementById('seller_id');
        const selectedSellerDiv = document.getElementById('selected_seller');
        const selectedSellerText = document.getElementById('selected_seller_text');
        let sellerDebounceTimer;

        sellerSearch.addEventListener('input', (e) => {
            clearTimeout(sellerDebounceTimer);
            const query = e.target.value.trim();

            if (query.length < 2) {
                sellerResults.classList.add('hidden');
                return;
            }

            sellerDebounceTimer = setTimeout(() => {
                fetch(`{{ route('staff.listings.search-sellers') }}?q=${encodeURIComponent(query)}`)
                    .then(response => response.json())
                    .then(users => {
                        if (users.length === 0) {
                            sellerResults.innerHTML = '<div class="p-4 text-gray-500">Nessun venditore trovato</div>';
                            sellerResults.classList.remove('hidden');
                            return;
                        }

                        sellerResults.innerHTML = users.map(user => `
                            <div class="p-4 border-b border-gray-200 hover:bg-gray-50 cursor-pointer transition" onclick="selectSeller(${user.id}, '${user.name.replace(/'/g, "\\'")} ${user.surname.replace(/'/g, "\\'")}')"}>
                                <p class="font-medium text-gray-900">${user.name} ${user.surname}</p>
                                <p class="text-sm text-gray-500">${user.email}</p>
                            </div>
                        `).join('');
                        sellerResults.classList.remove('hidden');
                    })
                    .catch(error => {
                        console.error('Errore nella ricerca:', error);
                        sellerResults.innerHTML = '<div class="p-4 text-red-500">Errore nella ricerca</div>';
                        sellerResults.classList.remove('hidden');
                    });
            }, 300);
        });

        function selectSeller(id, name) {
            sellerIdInput.value = id;
            sellerSearch.value = name;
            sellerResults.classList.add('hidden');
            selectedSellerText.textContent = name;
            selectedSellerDiv.classList.remove('hidden');
        }

        // === BOOK SEARCH ===
        const bookSearch = document.getElementById('book_search');
        const searchResults = document.getElementById('search_results');
        const bookIdInput = document.getElementById('book_id');
        const priceInput = document.getElementById('price');
        const selectedBookDiv = document.getElementById('selected_book');
        const selectedBookText = document.getElementById('selected_book_text');
        let debounceTimer;

        // Ricerca libri mentre digita
        bookSearch.addEventListener('input', (e) => {
            clearTimeout(debounceTimer);
            const query = e.target.value.trim();

            if (query.length < 2) {
                searchResults.classList.add('hidden');
                return;
            }

            debounceTimer = setTimeout(() => {
                fetch(`{{ route('staff.listings.search-books') }}?q=${encodeURIComponent(query)}`)
                    .then(response => response.json())
                    .then(books => {
                        if (books.length === 0) {
                            searchResults.innerHTML = '<div class="p-4 text-gray-500">Nessun libro trovato</div>';
                            searchResults.classList.remove('hidden');
                            return;
                        }

                        searchResults.innerHTML = books.map(book => `
                            <div class="p-4 border-b border-gray-200 hover:bg-gray-50 cursor-pointer transition" onclick="selectBook(${book.id}, '${book.title.replace(/'/g, "\\'")} - ${book.author.replace(/'/g, "\\'")}', ${book.original_price})">
                                <p class="font-medium text-gray-900">${book.title}</p>
                                <p class="text-sm text-gray-600">${book.author}</p>
                                <div class="flex justify-between items-center mt-1">
                                    ${book.isbn ? `<p class="text-xs text-gray-500">ISBN: ${book.isbn}</p>` : ''}
                                    <div class="text-right">
                                        <p class="text-xs text-gray-500 line-through">Prezzo: €${parseFloat(book.original_price).toFixed(2)}</p>
                                        <p class="text-sm font-semibold text-green-600">Acquisizione: €${Math.floor(book.original_price / 2).toFixed(2)}</p>
                                    </div>
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

        // Seleziona un libro
        function selectBook(id, title, originalPrice) {
            bookIdInput.value = id;
            bookSearch.value = title;
            // Prezzo = metà arrotondata all'intero inferiore
            priceInput.value = Math.floor(originalPrice / 2);
            searchResults.classList.add('hidden');
            selectedBookText.textContent = title;
            selectedBookDiv.classList.remove('hidden');
        }

        // Chiudi i dropdown quando clicca fuori
        document.addEventListener('click', (e) => {
            if (!e.target.closest('#seller_search') && !e.target.closest('#seller_results')) {
                sellerResults.classList.add('hidden');
            }
            if (!e.target.closest('#book_search') && !e.target.closest('#search_results')) {
                searchResults.classList.add('hidden');
            }
        });
    </script>
@endsection
