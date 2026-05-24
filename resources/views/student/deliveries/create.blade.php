@extends('layouts.app-student')

@section('title', 'Prenota Consegna')

@section('content')
    <div class="mb-8">
        <a href="{{ route('student.deliveries.index') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium mb-4 inline-block">
            ← Torna alle consegne
        </a>
        <h1 class="text-4xl font-bold text-gray-900">Prenota Consegna</h1>
        <p class="text-gray-600 mt-2">Seleziona uno o più libri da consegnare al mercatino</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- LEFT SIDE: FORM -->
        <div class="lg:col-span-2">
            <form id="delivery_form" class="bg-white rounded-lg shadow-sm border border-gray-200 p-8">
                @csrf

                <!-- Libro -->
                <div class="mb-8">
                    <label for="book_search" class="block text-sm font-semibold text-gray-900 mb-2">
                        Libro <span class="text-red-600">*</span>
                    </label>
                    <div class="relative">
                        <input 
                            type="text" 
                            id="book_search" 
                            placeholder="Cerca per ISBN, titolo o autore..." 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            autocomplete="off"
                        />
                        <input type="hidden" id="book_id" name="book_id" value="">
                        
                        <!-- Dropdown dei risultati -->
                        <div id="search_results" class="absolute top-full left-0 right-0 bg-white border border-gray-300 rounded-lg shadow-lg mt-2 max-h-64 overflow-y-auto hidden z-10"></div>
                    </div>
                    <p id="selected_book" class="text-sm text-gray-600 mt-2 hidden">
                        <span class="font-medium">Libro selezionato:</span> <span id="selected_book_text"></span>
                    </p>
                </div>

                <!-- Condizioni -->
                <div class="mb-8">
                    <label class="block text-sm font-semibold text-gray-900 mb-2">
                        Condizioni del libro <span class="text-red-600">*</span>
                    </label>
                    <div class="grid grid-cols-2 gap-4">
                        @foreach(['like-new' => 'Come Nuovo', 'good' => 'Buona', 'fair' => 'Discreta', 'poor' => 'Scarsa'] as $value => $label)
                            <label class="flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-blue-500 transition">
                                <input type="radio" id="condition_{{ $value }}" name="condition" value="{{ $value }}" class="w-4 h-4 text-blue-600">
                                <span class="ml-3 text-sm font-medium text-gray-900">{{ $label }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <!-- Prezzo Calcolato -->
                <div class="mb-8">
                    <label for="calculated_price" class="block text-sm font-semibold text-gray-900 mb-2">
                        Prezzo proposto (€)
                    </label>
                    <input type="text" id="calculated_price" class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50 focus:outline-none cursor-not-allowed" readonly placeholder="Seleziona un libro per vedere il prezzo">
                    <p class="text-gray-500 text-sm mt-2">Il prezzo è calcolato automaticamente come metà del prezzo originale del libro (arrotondato per difetto)</p>
                </div>

                <!-- Aggiungi al carrello -->
                <div class="flex items-center space-x-4">
                    <button type="button" onclick="addToCart()" class="flex-1 px-6 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition">
                        ➕ Aggiungi Libro
                    </button>
                    <a href="{{ route('student.deliveries.index') }}" class="px-6 py-3 text-gray-700 font-medium rounded-lg hover:bg-gray-100 transition">
                        Annulla
                    </a>
                </div>
            </form>
        </div>

        <!-- RIGHT SIDE: CART -->
        <div class="lg:col-span-1">
            <form id="batch_form" method="POST" action="{{ route('student.deliveries.batch.store') }}" class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 sticky top-6">
                @csrf
                <input type="hidden" id="cart_items_json" name="items" value="[]">

                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-lg font-bold text-gray-900">Riepilogo</h2>
                    <span id="cart_counter" class="inline-block bg-blue-600 text-white text-xs font-bold rounded-full w-8 h-8 flex items-center justify-center">0</span>
                </div>

                <div id="cart_items" class="space-y-3 mb-6 max-h-96 overflow-y-auto">
                    <p class="text-gray-500 text-sm text-center py-8">Nessun libro aggiunto</p>
                </div>

                <div class="border-t border-gray-200 pt-4 mb-6">
                    <div class="flex justify-between items-center mb-4">
                        <span class="text-gray-700 font-medium">Totale:</span>
                        <span id="cart_total" class="text-2xl font-bold text-blue-600">€0.00</span>
                    </div>
                </div>

                <div class="space-y-2">
                    <button type="submit" id="submit_btn" disabled class="w-full px-4 py-3 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition disabled:opacity-50 disabled:cursor-not-allowed">
                        ✓ Prenota Consegna
                    </button>
                    <button type="button" onclick="clearCart()" class="w-full px-4 py-2 bg-red-100 text-red-700 font-medium rounded-lg hover:bg-red-200 transition text-sm">
                        🗑️ Cancella Tutto
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        let debounceTimer;
        let cart = [];
        let currentBook = null;

        const bookSearch = document.getElementById('book_search');
        const bookIdInput = document.getElementById('book_id');
        const searchResults = document.getElementById('search_results');
        const selectedBookDisplay = document.getElementById('selected_book');
        const selectedBookText = document.getElementById('selected_book_text');
        const priceInput = document.getElementById('calculated_price');
        const cartItemsDisplay = document.getElementById('cart_items');
        const cartCounter = document.getElementById('cart_counter');
        const cartTotal = document.getElementById('cart_total');
        const cartItemsJson = document.getElementById('cart_items_json');
        const submitBtn = document.getElementById('submit_btn');

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
                        searchResults.innerHTML = '';
                        if (books.length === 0) {
                            searchResults.innerHTML = '<div class="px-4 py-3 text-gray-500 text-sm">Nessun libro trovato</div>';
                        } else {
                            books.forEach(book => {
                                const div = document.createElement('div');
                                div.className = 'px-4 py-3 hover:bg-blue-50 cursor-pointer border-b last:border-b-0';
                                div.innerHTML = `
                                    <p class="font-medium text-gray-900">${book.title}</p>
                                    <p class="text-sm text-gray-600">${book.author}</p>
                                    <p class="text-xs text-gray-500">ISBN: ${book.isbn}</p>
                                `;
                                div.onclick = () => selectBook(book);
                                searchResults.appendChild(div);
                            });
                        }
                        searchResults.classList.remove('hidden');
                    });
            }, 300);
        });

        // Select book
        function selectBook(book) {
            currentBook = book;
            bookIdInput.value = book.id;
            bookSearch.value = `${book.title} - ${book.author}`;
            selectedBookText.textContent = `${book.title} by ${book.author}`;
            selectedBookDisplay.classList.remove('hidden');
            searchResults.classList.add('hidden');
            
            // Calculate price
            const condition = document.querySelector('input[name="condition"]:checked');
            updatePrice();
        }

        // Update price when condition changes
        document.querySelectorAll('input[name="condition"]').forEach(input => {
            input.addEventListener('change', updatePrice);
        });

        function updatePrice() {
            if (currentBook) {
                const price = Math.floor(currentBook.original_price / 2);
                priceInput.value = '€' + price.toFixed(2);
            }
        }

        // Add to cart
        function addToCart() {
            const bookId = bookIdInput.value;
            const condition = document.querySelector('input[name="condition"]:checked');

            if (!bookId) {
                alert('Seleziona un libro');
                return;
            }

            if (!condition) {
                alert('Seleziona le condizioni del libro');
                return;
            }

            // Check if book already in cart
            if (cart.some(item => item.id === currentBook.id)) {
                alert('Questo libro è già nel carrello');
                return;
            }

            const item = {
                book_id: currentBook.id,
                id: currentBook.id,
                title: currentBook.title,
                author: currentBook.author,
                isbn: currentBook.isbn,
                condition: condition.value,
                price: Math.floor(currentBook.original_price / 2),
            };

            cart.push(item);
            updateCartDisplay();

            // Reset form
            bookSearch.value = '';
            bookIdInput.value = '';
            selectedBookDisplay.classList.add('hidden');
            document.querySelectorAll('input[name="condition"]').forEach(input => input.checked = false);
            priceInput.value = '';
            currentBook = null;
            bookSearch.focus();
        }

        function removeFromCart(bookId) {
            cart = cart.filter(item => item.id !== bookId);
            updateCartDisplay();
        }

        function updateCartDisplay() {
            cartCounter.textContent = cart.length;
            
            if (cart.length === 0) {
                cartItemsDisplay.innerHTML = '<p class="text-gray-500 text-sm text-center py-8">Nessun libro aggiunto</p>';
                cartTotal.textContent = '€0.00';
                cartItemsJson.value = '[]';
                submitBtn.disabled = true;
            } else {
                cartItemsDisplay.innerHTML = cart.map((item, index) => `
                    <div class="p-4 bg-gray-50 rounded-lg border border-gray-200">
                        <div class="flex justify-between items-start mb-2">
                            <div class="flex-1">
                                <p class="font-medium text-gray-900 text-sm">${item.title}</p>
                                <p class="text-xs text-gray-600">${item.author}</p>
                                <p class="text-xs text-gray-500 mt-1">
                                    <span class="inline-block bg-blue-100 text-blue-700 px-2 py-1 rounded">
                                        ${item.condition === 'like-new' ? 'Come Nuovo' : item.condition === 'good' ? 'Buona' : item.condition === 'fair' ? 'Discreta' : 'Scarsa'}
                                    </span>
                                </p>
                            </div>
                            <button type="button" onclick="removeFromCart(${item.id})" class="text-red-600 hover:text-red-800 font-bold text-lg">×</button>
                        </div>
                        <div class="text-right">
                            <p class="text-lg font-bold text-blue-600">€${item.price.toFixed(2)}</p>
                        </div>
                    </div>
                `).join('');

                const total = cart.reduce((sum, item) => sum + item.price, 0);
                cartTotal.textContent = '€' + total.toFixed(2);
                
                // Update hidden input for form submission
                const itemsForSubmit = cart.map(item => ({
                    book_id: item.book_id,
                    condition: item.condition
                }));
                cartItemsJson.value = JSON.stringify(itemsForSubmit);
                submitBtn.disabled = false;
            }
        }

        function clearCart() {
            if (confirm('Sei sicuro di voler cancellare tutti i libri dal carrello?')) {
                cart = [];
                updateCartDisplay();
            }
        }

        // Form submission handler
        document.getElementById('batch_form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            if (cart.length === 0) {
                alert('Aggiungi almeno un libro al carrello');
                return;
            }

            // Form will submit normally with the hidden input already populated
            this.submit();
        });
    </script>
@endsection