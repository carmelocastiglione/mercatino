@extends('layouts.app-student')

@section('title', 'Prenota Libri Acquisiti')

@section('content')
    <div class="mb-8">
        <a href="{{ route('student.book-reservations.index') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium mb-4 inline-block">
            ← Torna alle prenotazioni
        </a>
        <h1 class="text-4xl font-bold text-gray-900">Prenota Libri Acquisiti</h1>
        <p class="text-gray-600 mt-2">Seleziona uno o più libri dai nostri acquisiti. I libri verranno messi in sospeso per la conferma dello staff.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- LEFT SIDE: FORM -->
        <div class="lg:col-span-2">
            <form id="reservation_form" method="POST" action="{{ route('student.book-reservations.store') }}" class="bg-white rounded-lg shadow-sm border border-gray-200 p-8" onsubmit="return handleFormSubmit(event)">
                @csrf

                <!-- Availability error message box -->
                <div id="availability_error" class="mb-6 p-4 bg-red-50 border border-red-300 rounded-lg hidden">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p id="availability_error_message" class="text-sm font-medium text-red-700"></p>
                        </div>
                    </div>
                </div>

                <!-- Error message box -->
                @if ($errors->any())
                    <div class="mb-6 p-4 bg-red-50 border border-red-300 rounded-lg">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-red-700">
                                    @foreach ($errors->all() as $error)
                                        {{ $error }}<br>
                                    @endforeach
                                </p>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Ricerca Libri -->
                <div class="mb-8">
                    <label for="book_search" class="block text-sm font-semibold text-gray-900 mb-2">
                        Ricerca Libro <span class="text-red-600">*</span>
                    </label>
                    <div class="relative">
                        <input 
                            type="text" 
                            id="book_search" 
                            placeholder="Cerca per ISBN, titolo o autore..." 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            autocomplete="off"
                        />
                        
                        <!-- Dropdown dei risultati -->
                        <div id="search_results" class="absolute top-full left-0 right-0 bg-white border border-gray-300 rounded-lg shadow-lg mt-2 max-h-64 overflow-y-auto hidden z-10"></div>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Digita almeno 2 caratteri per cercare</p>
                </div>

                <!-- Lista di Libri Selezionati -->
                <div class="mb-8">
                    <label class="block text-sm font-semibold text-gray-900 mb-4">
                        Libri Selezionati <span class="text-red-600">*</span>
                        <span id="selected_count" class="text-gray-500 font-normal">(0)</span>
                    </label>
                    
                    <div id="book_listing_ids_container"></div>
                    
                    <div id="selected_books_list" class="space-y-2">
                        <div class="p-4 text-center bg-gray-50 border-2 border-dashed border-gray-300 rounded-lg">
                            <p class="text-sm text-gray-500">Nessun libro selezionato</p>
                        </div>
                    </div>
                </div>

                <!-- Note -->
                <div class="mb-8">
                    <label for="notes" class="block text-sm font-semibold text-gray-900 mb-2">
                        Note (opzionale)
                    </label>
                    <textarea 
                        id="notes" 
                        name="notes" 
                        rows="3" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        placeholder="Aggiungi eventuali note sulla tua prenotazione..."
                    ></textarea>
                </div>

                <!-- Submit Button -->
                <button 
                    type="submit" 
                    id="submit_btn"
                    disabled
                    class="w-full bg-blue-600 hover:bg-blue-700 disabled:bg-gray-400 text-white font-semibold py-3 rounded-lg transition"
                >
                    Crea Prenotazione
                </button>
            </form>
        </div>

        <!-- RIGHT SIDE: SUMMARY -->
        <div>
            <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-lg p-6 border border-blue-200 sticky top-4">
                <h3 class="font-bold text-lg text-gray-900 mb-4">Riepilogo Prenotazione</h3>
                
                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Numero libri:</span>
                        <span id="summary_count" class="font-bold text-lg text-gray-900">0</span>
                    </div>
                    
                    <div class="flex justify-between items-center pt-3 border-t border-blue-200">
                        <span class="text-gray-600">Prezzo totale:</span>
                        <span id="summary_price" class="font-bold text-2xl text-blue-600">€0.00</span>
                    </div>
                </div>

                <div class="mt-6 p-4 bg-blue-100 border border-blue-300 rounded-lg">
                    <p class="text-sm text-blue-900">
                        <strong>Nota:</strong> La tua prenotazione verrà messa in sospeso. Lo staff esaminerà la richiesta e la confermerà o rifiuterà.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <script>
        const selectedBooks = new Map();
        const bookSearchInput = document.getElementById('book_search');
        const searchResultsDiv = document.getElementById('search_results');
        const selectedBooksList = document.getElementById('selected_books_list');
        const bookListingIdsContainer = document.getElementById('book_listing_ids_container');
        const submitBtn = document.getElementById('submit_btn');
        const selectedCount = document.getElementById('selected_count');
        const summaryCount = document.getElementById('summary_count');
        const summaryPrice = document.getElementById('summary_price');

        let searchTimeout;

        // Ricerca libri
        bookSearchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            const query = this.value.trim();

            if (query.length < 2) {
                searchResultsDiv.classList.add('hidden');
                return;
            }

            searchTimeout = setTimeout(() => {
                fetch(`{{ route('student.book-reservations.search-acquisition-books') }}?q=${encodeURIComponent(query)}`)
                    .then(response => response.json())
                    .then(books => {
                        if (books.length === 0) {
                            searchResultsDiv.innerHTML = '<div class="p-4 text-sm text-gray-500">Nessun libro trovato</div>';
                        } else {
                            searchResultsDiv.innerHTML = books.map(book => {
                                const bookId = book.id;
                                const bookTitle = book.title.replace(/'/g, "\\'");
                                const bookAuthor = book.author.replace(/'/g, "\\'");
                                const bookPrice = book.price;
                                const bookCondition = book.condition;
                                const bookIsbn = book.isbn;
                                
                                const conditionLabels = {
                                    'like-new': 'Come Nuovo',
                                    'good': 'Buona',
                                    'fair': 'Discreta',
                                    'poor': 'Scarsa'
                                };

                                const conditionBadgeClasses = {
                                    'like-new': 'bg-green-100 text-green-800',
                                    'good': 'bg-blue-100 text-blue-800',
                                    'fair': 'bg-yellow-100 text-yellow-800',
                                    'poor': 'bg-red-100 text-red-800'
                                };
                                
                                return `
                                <div class="p-4 border-b border-gray-200 hover:bg-gray-50 cursor-pointer" onclick="selectBook(${bookId}, '${bookTitle}', '${bookAuthor}', ${bookPrice}, '${bookCondition}', '${bookIsbn}')">
                                    <div class="font-medium text-gray-900">${book.title}</div>
                                    <div class="text-sm text-gray-600">di ${book.author}</div>
                                    <div class="text-xs text-gray-500">ISBN: ${book.isbn}</div>
                                    <div class="flex justify-between items-center mt-2">
                                        <span class="text-sm font-semibold text-gray-700">€${parseFloat(book.price).toFixed(2)}</span>
                                        <span class="px-2 py-1 rounded-full text-xs font-semibold ${conditionBadgeClasses[bookCondition] || 'bg-gray-100 text-gray-800'}">${conditionLabels[bookCondition] || bookCondition}</span>
                                    </div>
                                </div>
                                `;
                            }).join('');
                        }
                        searchResultsDiv.classList.remove('hidden');
                    });
            }, 300);
        });

        // Seleziona libro
        function selectBook(id, title, author, price, condition, isbn) {
            if (!selectedBooks.has(id)) {
                selectedBooks.set(id, { title, author, price: parseFloat(price), condition, isbn });
                updateUI();
                bookSearchInput.value = '';
                searchResultsDiv.classList.add('hidden');
            }
        }

        // Rimuovi libro
        function removeBook(id) {
            selectedBooks.delete(id);
            updateUI();
        }

        // Aggiorna UI
        function updateUI() {
            // Update lista
            if (selectedBooks.size === 0) {
                selectedBooksList.innerHTML = `
                    <div class="p-4 text-center bg-gray-50 border-2 border-dashed border-gray-300 rounded-lg">
                        <p class="text-sm text-gray-500">Nessun libro selezionato</p>
                    </div>
                `;
            } else {
                selectedBooksList.innerHTML = Array.from(selectedBooks.entries()).map(([id, book]) => {
                    const conditionLabels = {
                        'like-new': 'Come Nuovo',
                        'good': 'Buona',
                        'fair': 'Discreta',
                        'poor': 'Scarsa'
                    };

                    const conditionBadgeClasses = {
                        'like-new': 'bg-green-100 text-green-800',
                        'good': 'bg-blue-100 text-blue-800',
                        'fair': 'bg-yellow-100 text-yellow-800',
                        'poor': 'bg-red-100 text-red-800'
                    };
                    
                    return `
                    <div class="p-4 bg-gray-50 border border-gray-200 rounded-lg flex justify-between items-start">
                        <div class="flex-1">
                            <div class="font-medium text-gray-900">${book.title}</div>
                            <div class="text-sm text-gray-600">di ${book.author}</div>
                            <div class="text-xs text-gray-600 mt-1">ISBN: ${book.isbn}</div>
                            <div class="flex items-center gap-3 mt-2">
                                <span class="text-sm font-semibold text-blue-600">€${book.price.toFixed(2)}</span>
                                <span class="px-2 py-1 rounded-full text-xs font-semibold ${conditionBadgeClasses[book.condition] || 'bg-gray-100 text-gray-800'}">${conditionLabels[book.condition] || book.condition}</span>
                            </div>
                        </div>
                        <button type="button" onclick="removeBook(${id})" class="text-red-600 hover:text-red-800 font-medium text-sm ml-4">
                            Rimuovi
                        </button>
                    </div>
                `;
                }).join('');
            }

            // Update hidden inputs (create input fields for each selected book)
            bookListingIdsContainer.innerHTML = Array.from(selectedBooks.keys()).map(id => 
                `<input type="hidden" name="book_listing_ids[]" value="${id}">`
            ).join('');

            // Update counter e summary
            selectedCount.textContent = `(${selectedBooks.size})`;
            summaryCount.textContent = selectedBooks.size;

            // Update totale prezzo
            const totalPrice = Array.from(selectedBooks.values()).reduce((sum, book) => sum + book.price, 0);
            summaryPrice.textContent = `€${totalPrice.toFixed(2)}`;

            // Enable/disable submit
            submitBtn.disabled = selectedBooks.size === 0;
        }

        // Close dropdown quando clicca fuori
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.relative')) {
                searchResultsDiv.classList.add('hidden');
            }
        });

        // Valida disponibilità prima del submit
        async function handleFormSubmit(event) {
            event.preventDefault();
            const availabilityErrorDiv = document.getElementById('availability_error');
            const availabilityErrorMessage = document.getElementById('availability_error_message');

            // Nascondi il box di errore
            availabilityErrorDiv.classList.add('hidden');

            // Controlla se ci sono libri selezionati
            if (selectedBooks.size === 0) {
                availabilityErrorMessage.textContent = 'Seleziona almeno un libro prima di prenotare.';
                availabilityErrorDiv.classList.remove('hidden');
                return false;
            }

            // Raccogli gli IDs dei libri selezionati
            const bookListingIds = Array.from(selectedBooks.keys());

            // Chiama l'endpoint per verificare la disponibilità
            try {
                const response = await fetch('{{ route("student.book-reservations.check-availability") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                    },
                    body: JSON.stringify({ book_listing_ids: bookListingIds })
                });

                const data = await response.json();

                if (!data.available) {
                    // Mostra il messaggio di errore
                    availabilityErrorMessage.textContent = data.message;
                    availabilityErrorDiv.classList.remove('hidden');
                    return false;
                }

                // Tutti i libri sono disponibili, sottometti il form
                document.getElementById('reservation_form').submit();
            } catch (error) {
                console.error('Errore durante il controllo della disponibilità:', error);
                availabilityErrorMessage.textContent = 'Errore durante il controllo della disponibilità. Riprova più tardi.';
                availabilityErrorDiv.classList.remove('hidden');
                return false;
            }
        }
    </script>
@endsection
