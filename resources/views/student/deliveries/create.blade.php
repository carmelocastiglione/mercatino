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

                <!-- Error message box -->
                <div id="error_box" class="hidden mb-6 p-4 bg-red-50 border border-red-300 rounded-lg">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p id="error_message" class="text-sm font-medium text-red-700"></p>
                        </div>
                        <button type="button" onclick="hideError()" class="ml-auto text-red-400 hover:text-red-600">
                            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Info Box: Multiple Books Selection -->
                    <x-info-box
                        type="info"
                        title="Selezione di più libri"
                        message="Puoi selezionare uno o più libri da consegnare al mercatino da questo modulo. Aggiungi ogni libro utilizzando il modulo sottostante e al termine inviali insieme per un'unica consegna."
                    />

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
                            class="w-full px-4 py-2 pr-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            autocomplete="off"
                        />
                        <button
                            type="button"
                            id="clear_search_btn"
                            onclick="clearSearch()"
                            class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600 hidden"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                        <input type="hidden" id="book_id" name="book_id" value="">
                        
                        <!-- Dropdown dei risultati -->
                        <div id="search_results" class="absolute top-full left-0 right-0 bg-white border border-gray-300 rounded-lg shadow-lg mt-2 max-h-64 overflow-y-auto hidden z-10"></div>
                    </div>
                </div>

                <!-- Dettagli Libro Selezionato -->
                <div id="selected_book_box" class="hidden mb-8 bg-gradient-to-br from-blue-50 to-indigo-50 rounded-lg p-6 border border-blue-200">
                    <h3 class="text-sm font-bold text-gray-900 mb-4">Libro Selezionato</h3>
                    <div class="space-y-3">
                        <div>
                            <p class="text-xs text-gray-600 font-medium">TITOLO</p>
                            <p id="book_detail_title" class="text-base font-bold text-gray-900"></p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-600 font-medium">AUTORE</p>
                            <p id="book_detail_author" class="text-sm text-gray-700"></p>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-xs text-gray-600 font-medium">ISBN</p>
                                <p id="book_detail_isbn" class="text-sm font-mono text-gray-700"></p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-600 font-medium">PREZZO COPERTINA</p>
                                <p id="book_detail_cover_price" class="text-sm font-bold text-green-600"></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Condizioni -->
                <div class="mb-8">
                    <div class="flex items-center gap-2 mb-2 relative group">
                        <label class="block text-sm font-semibold text-gray-900">
                            Condizioni del libro <span class="text-red-600">*</span>
                        </label>
                        <!-- Info Icon -->
                        <div class="relative inline-block">
                            <button type="button" class="flex items-center justify-center w-5 h-5 rounded-full bg-indigo-300 text-white text-xs font-bold hover:bg-indigo-400 transition" onclick="event.preventDefault()">
                                i
                            </button>
                            <!-- Tooltip -->
                            <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-3 hidden group-hover:block w-64 bg-gray-900 text-white text-xs rounded-lg p-3 shadow-xl z-20 pointer-events-none">
                                <div class="space-y-2">
                                    <div>
                                        <span class="font-semibold">Come Nuovo:</span> Il libro non presenta sottolineature o scritte di nessun tipo, non ha esercizi svolti, la copertina è integra e nessuna pagina è strappata o mancante. Sono presenti tutti gli eventuali allegati.
                                    </div>
                                    <div>
                                        <span class="font-semibold">Buona:</span> Il libro presenta sottolineature a matita, gli esercizi sono svolti a matita, la copertina è integra e qualche pagina è strappata o mancante. Mancano alcuni o tutti gli eventuali allegati.
                                    </div>
                                    <div>
                                        <span class="font-semibold">Discreta:</span> Il libro presenta evidenziature colorate, esercizi svolti in matita o penna. La copertina è integra e qualche pagina è strappata o mancante. Mancano alcuni o tutti gli eventuali allegati.
                                    </div>
                                    <div>
                                        <span class="font-semibold">Scarsa:</span> Il libro presenta evidenziature e scritte colorate, esercizi svolti in penna, la copertina NON è integra e ci sono pagine strappate o mancanti. Mancano tutti gli eventuali allegati.
                                    </div>
                                    <div>
                                        <span class="font-semibold">Nota:</span> Ci riserviamo il diritto di non accettare libri in condizioni troppo compromesse.
                                    </div>
                                </div>
                                <!-- Arrow -->
                                <div class="absolute top-full left-1/2 transform -translate-x-1/2 -mt-1 w-0 h-0 border-l-4 border-r-4 border-t-4 border-l-transparent border-r-transparent border-t-gray-900"></div>
                            </div>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        @foreach(['like-new' => 'Come Nuovo', 'good' => 'Buona', 'fair' => 'Discreta', 'poor' => 'Scarsa'] as $value => $label)
                            <label class="flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-blue-500 transition">
                                <input type="radio" id="condition_{{ $value }}" name="condition" value="{{ $value }}" class="w-4 h-4 text-blue-600" @if($value === 'like-new') checked @endif>
                                <span class="ml-3 text-sm font-medium text-gray-900">{{ $label }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <!-- Prezzo Calcolato -->
                <div class="mb-8" id="price_details_section" style="display: none;">
                    <label class="block text-sm font-semibold text-gray-900 mb-4">
                        Dettagli Prezzo
                    </label>
                    <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-lg p-4 border border-blue-200">
                        <div class="space-y-3">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Prezzo copertina:</span>
                                <span class="font-medium text-gray-900" id="price_original">€0.00</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Prezzo mercatino:</span>
                                <span class="font-medium text-gray-900" id="price_marketplace">€0.00</span>
                            </div>
                            <div class="flex justify-between items-center pt-2 border-t border-blue-200">
                                <span class="text-gray-600">Commissione applicata:</span>
                                <span class="font-medium text-red-600" id="price_fee">-€0.00</span>
                            </div>
                            <div class="flex justify-between items-center pt-3 border-t-2 border-blue-300 mt-3">
                                <span class="font-bold text-gray-900">Totale da ricevere:</span>
                                <span class="text-2xl font-bold text-blue-600" id="price_total">€0.00</span>
                            </div>
                        </div>
                    </div>

                    <!-- Info box 
                    <div class="mt-6">
                        <x-info-box
                            type="info"
                            title="Nota sul prezzo"
                            message="Il prezzo è indicativo e potrebbe subire modifiche in base alle reali condizioni del libro a discrezione dello staff."
                        />
                    </div>
                    -->
                </div>

                <!-- Aggiungi al carrello -->
                <div class="flex items-center space-x-4">
                    <button type="button" onclick="addToCart()" class="flex-1 px-6 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition">
                        Aggiungi Libro
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
                    <span id="cart_counter" class="flex items-center justify-center bg-blue-600 text-white text-xs font-bold rounded-full w-8 h-8 leading-none">0</span>
                </div>

                <!-- Data di consegna -->
                <div class="mb-6">
                    <label for="scheduled_delivery_date_id" class="block text-sm font-semibold text-gray-900 mb-2">
                        Data di consegna
                    </label>
                    <select id="scheduled_delivery_date_id" name="scheduled_delivery_date_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">-- Seleziona data --</option>
                    </select>
                    <p id="no_dates_message" class="text-center text-sm text-gray-600 mt-2 hidden">
                        <span class="inline-block bg-yellow-100 text-yellow-700 px-3 py-1 rounded-full text-xs font-medium">ℹ️ Consegna nel giorno concordato</span>
                    </p>
                </div>

                <div id="cart_items" class="space-y-3 mb-6">
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
                        Prenota Consegna
                    </button>
                    <button type="button" onclick="clearCart()" class="w-full px-4 py-2 bg-red-100 text-red-700 font-medium rounded-lg hover:bg-red-200 transition text-sm">
                        Cancella Tutto
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
        const cartItemsDisplay = document.getElementById('cart_items');
        const cartCounter = document.getElementById('cart_counter');
        const cartTotal = document.getElementById('cart_total');
        const cartItemsJson = document.getElementById('cart_items_json');
        const submitBtn = document.getElementById('submit_btn');
        const errorBox = document.getElementById('error_box');
        const errorMessage = document.getElementById('error_message');
        const deliveryDateSelect = document.getElementById('scheduled_delivery_date_id');
        const noDatesMessage = document.getElementById('no_dates_message');

        // Load delivery dates on page load
        document.addEventListener('DOMContentLoaded', function() {
            loadDeliveryDates();
        });

        // Load available delivery dates from the server
        function loadDeliveryDates() {
            fetch(`{{ route('student.deliveries.delivery-dates') }}`)
                .then(response => response.json())
                .then(data => {
                    deliveryDateSelect.innerHTML = '<option value="">-- Seleziona data --</option>';
                    
                    if (data.has_dates) {
                        noDatesMessage.classList.add('hidden');
                        data.dates.forEach(date => {
                            const option = document.createElement('option');
                            option.value = date.id;
                            option.textContent = date.label;
                            deliveryDateSelect.appendChild(option);
                        });
                    } else {
                        noDatesMessage.classList.remove('hidden');
                        deliveryDateSelect.disabled = true;
                    }
                })
                .catch(error => {
                    console.error('Error loading delivery dates:', error);
                    showError('Errore nel caricamento delle date di consegna');
                });
        }

        // Show error message in the right panel
        function showError(message) {
            errorMessage.textContent = message;
            errorBox.classList.remove('hidden');
            // Auto-hide after 5 seconds
            setTimeout(() => {
                errorBox.classList.add('hidden');
            }, 5000);
        }

        // Hide error message
        function hideError() {
            errorBox.classList.add('hidden');
        }

        // Clear search
        function clearSearch() {
            bookSearch.value = '';
            bookIdInput.value = '';
            document.getElementById('clear_search_btn').classList.add('hidden');
            document.getElementById('selected_book_box').classList.add('hidden');
            document.getElementById('price_details_section').style.display = 'none';
            searchResults.classList.add('hidden');
            // Reset condition to "Come Nuovo"
            document.getElementById('condition_like-new').checked = true;
            bookSearch.focus();
        }

        // Search functionality
        bookSearch.addEventListener('input', (e) => {
            const clearBtn = document.getElementById('clear_search_btn');
            if (e.target.value.trim().length > 0) {
                clearBtn.classList.remove('hidden');
            } else {
                clearBtn.classList.add('hidden');
            }

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
            
            // Update book details box
            document.getElementById('book_detail_title').textContent = book.title;
            document.getElementById('book_detail_author').textContent = book.author;
            document.getElementById('book_detail_isbn').textContent = book.isbn;
            document.getElementById('book_detail_cover_price').textContent = '€' + book.original_price.toFixed(2);
            document.getElementById('selected_book_box').classList.remove('hidden');
            
            searchResults.classList.add('hidden');
            
            // Reset condition to "Come Nuovo"
            document.getElementById('condition_like-new').checked = true;
            
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
                const priceDetailsSection = document.getElementById('price_details_section');
                document.getElementById('price_original').textContent = '€' + currentBook.original_price.toFixed(2);
                document.getElementById('price_marketplace').textContent = '€' + currentBook.marketplace_price.toFixed(2);
                document.getElementById('price_fee').textContent = '-€' + currentBook.fee.toFixed(2);
                document.getElementById('price_total').textContent = '€' + currentBook.price.toFixed(2);
                priceDetailsSection.style.display = 'block';
            }
        }

        // Add to cart
        function addToCart() {
            const bookId = bookIdInput.value;
            const condition = document.querySelector('input[name="condition"]:checked');

            if (!bookId) {
                showError('Seleziona un libro');
                return;
            }

            if (!condition) {
                showError('Seleziona le condizioni del libro');
                return;
            }

            // Check if book already in cart
            if (cart.some(item => item.book_id === currentBook.id)) {
                showError('Questo libro è già nel carrello');
                return;
            }

            const item = {
                book_id: currentBook.id,
                title: currentBook.title,
                author: currentBook.author,
                isbn: currentBook.isbn,
                condition: condition.value,
                original_price: currentBook.original_price,
                marketplace_price: currentBook.marketplace_price,
                fee: currentBook.fee,
                price: currentBook.price,
            };

            cart.push(item);
            updateCartDisplay();

            // Reset form
            bookSearch.value = '';
            bookIdInput.value = '';
            document.getElementById('selected_book_box').classList.add('hidden');
            // Reset condition to "Come Nuovo"
            document.getElementById('condition_like-new').checked = true;
            document.getElementById('price_details_section').style.display = 'none';
            currentBook = null;
            bookSearch.focus();
        }

        function removeFromCart(bookId) {
            cart = cart.filter(item => item.book_id !== bookId);
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
                                <p class="text-xs text-gray-500">ISBN: ${item.isbn}</p>
                                <p class="text-xs text-gray-500 mt-1">
                                    <span class="inline-block bg-blue-100 text-blue-700 px-2 py-1 rounded">
                                        ${item.condition === 'like-new' ? 'Come Nuovo' : item.condition === 'good' ? 'Buona' : item.condition === 'fair' ? 'Discreta' : 'Scarsa'}
                                    </span>
                                </p>
                            </div>
                            <button type="button" onclick="removeFromCart(${item.book_id})" class="text-red-600 hover:text-red-800 font-bold text-lg">×</button>
                        </div>
                        <div class="text-right pt-2 border-t border-gray-200">
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
                return false;
            }
            
            // Controlla se la data di consegna è obbligatoria
            const deliveryDateSelect = document.getElementById('scheduled_delivery_date_id');
            const noDatesMessage = document.getElementById('no_dates_message');
            
            // Se il messaggio "nessuna data" è nascosto, significa che ci sono date disponibili
            if (noDatesMessage.classList.contains('hidden')) {
                // Ci sono date disponibili, quindi deve essere selezionata una
                if (!deliveryDateSelect.value) {
                    alert('Seleziona una data di consegna');
                    deliveryDateSelect.focus();
                    return false;
                }
            }
            
            // Prepare items JSON
            const itemsForSubmit = cart.map(item => ({
                book_id: item.book_id,
                condition: item.condition
            }));
            
            // Set the hidden input value
            document.getElementById('cart_items_json').value = JSON.stringify(itemsForSubmit);
            
            // Submit the form
            this.submit();
        });
    </script>
@endsection