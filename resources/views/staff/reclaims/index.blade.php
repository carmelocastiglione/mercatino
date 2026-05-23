@extends('layouts.app-staff')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-4xl font-bold text-gray-900">{{ $title }}</h1>
        <p class="text-gray-600 mt-2">{{ $description }}</p>
    </div>

    <!-- Form di ricerca acquirente -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-8">
        <div class="relative">
            <label class="block text-sm font-medium text-gray-700 mb-3">Cerca Acquirente</label>
            <input 
                type="text" 
                id="buyer_search" 
                placeholder="Digita nome, cognome, email o codice acquirente..." 
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
            />
            
            <!-- Dropdown risultati ricerca -->
            <div id="buyer_results" class="hidden absolute top-full left-0 right-0 mt-1 bg-white border border-gray-300 rounded-lg shadow-lg z-50 max-h-64 overflow-y-auto">
                <!-- Risultati caricati via JS -->
            </div>
        </div>

        <div id="buyer_info_box" class="hidden mt-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm font-medium text-gray-700">Acquirente Selezionato:</p>
                    <p id="buyer_name_display" class="text-lg font-bold text-gray-900 mt-1"></p>
                    <p id="buyer_email_display" class="text-sm text-gray-600"></p>
                    <p id="buyer_code_display" class="text-sm text-gray-600"></p>
                </div>
                <button type="button" onclick="clearBuyerSearch()" class="px-3 py-1 bg-gray-400 text-white text-sm font-medium rounded hover:bg-gray-500 transition">
                    ✕ Cambia
                </button>
            </div>
        </div>
    </div>

    <!-- Lista libri acquistati -->
    <div id="buyer_books_box" class="hidden">
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-900">
                Libri Acquistati <span id="books_count" class="ml-2 inline-block bg-purple-600 text-white text-sm font-bold rounded-full px-3 py-1">0</span>
            </h2>
        </div>

        <div id="buyer_books_list" class="divide-y divide-gray-200">
            <!-- Libri caricati via JS -->
        </div>
    </div>

    <!-- Messaggio iniziale -->
    <div id="initial_message" class="bg-white rounded-lg shadow-sm border border-gray-200 p-8 text-center">
        <p class="text-gray-500 text-lg">Cerca un acquirente per visualizzare i libri acquistati</p>
    </div>
</div>

<script>
    let buyerDebounceTimer;
    let currentBuyerId = null;
    let currentBuyerData = null;  // Memorizza i dati dell'acquirente
    let buyerBooks = [];

    const buyerSearch = document.getElementById('buyer_search');
    const buyerResults = document.getElementById('buyer_results');
    const buyerInfoBox = document.getElementById('buyer_info_box');
    const buyerBooksBox = document.getElementById('buyer_books_box');
    const initialMessage = document.getElementById('initial_message');
    const buyerBooksList = document.getElementById('buyer_books_list');
    const booksCount = document.getElementById('books_count');

    // Search con debounce
    buyerSearch.addEventListener('input', (e) => {
        clearTimeout(buyerDebounceTimer);
        const query = e.target.value.trim();

        if (!query) {
            buyerResults.classList.add('hidden');
            return;
        }

        buyerDebounceTimer = setTimeout(() => {
            fetch(`{{ route('staff.reclaims.search-buyers') }}?q=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(data => {
                    if (data.buyers.length === 0) {
                        buyerResults.innerHTML = '<div class="p-3 text-gray-500 text-sm">Nessun risultato</div>';
                        buyerResults.classList.remove('hidden');
                        return;
                    }

                    buyerResults.innerHTML = data.buyers.map(buyer => `
                        <div onclick="selectBuyer(${buyer.id}, '${buyer.name}', '${buyer.surname}', '${buyer.email}', '${buyer.code}')" class="p-3 hover:bg-gray-100 cursor-pointer border-b border-gray-100">
                            <p class="font-medium text-gray-900">${buyer.name} ${buyer.surname}</p>
                            <p class="text-xs text-gray-600">${buyer.email} • ${buyer.code}</p>
                        </div>
                    `).join('');
                    buyerResults.classList.remove('hidden');
                })
                .catch(error => console.error('Errore:', error));
        }, 300);
    });

    function selectBuyer(id, name, surname, email, code) {
        currentBuyerId = id;
        currentBuyerData = { id, name, surname, email, code };  // Salva i dati
        buyerSearch.value = `${name} ${surname}`;
        buyerResults.classList.add('hidden');
        
        document.getElementById('buyer_name_display').textContent = `${name} ${surname}`;
        document.getElementById('buyer_email_display').textContent = email;
        document.getElementById('buyer_code_display').textContent = code;
        
        buyerInfoBox.classList.remove('hidden');
        initialMessage.classList.add('hidden');

        // Carica i libri acquistati
        fetch(`{{ route('staff.reclaims.buyer-books') }}?buyer_id=${id}`)
            .then(response => {
                if (!response.ok) throw new Error(`HTTP ${response.status}`);
                return response.json();
            })
            .then(data => {
                console.log('Books data:', data);  // Debug
                buyerBooks = data.books || [];
                booksCount.textContent = buyerBooks.length;

                if (buyerBooks.length === 0) {
                    buyerBooksList.innerHTML = '<div class="p-8 text-center text-gray-500">Questo acquirente non ha libri acquistati</div>';
                    buyerBooksBox.classList.remove('hidden');
                    return;
                }

                const conditionColors = {
                    'like-new': 'bg-green-100 text-green-800',
                    'good': 'bg-blue-100 text-blue-800',
                    'fair': 'bg-yellow-100 text-yellow-800',
                    'poor': 'bg-red-100 text-red-800'
                };

                buyerBooksList.innerHTML = buyerBooks.map((book) => {
                    const hasReclaim = data.reclaims && data.reclaims.some(r => r.book_listing_id === book.id);
                    return `
                        <div class="p-4 bg-white hover:bg-gray-50 transition border-b border-gray-100">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <p class="font-medium text-gray-900">${book.title}</p>
                                    <p class="text-sm text-gray-600">${book.author || 'Autore sconosciuto'}</p>
                                    <div class="flex gap-2 mt-2">
                                        <span class="inline-block px-2 py-1 text-xs font-semibold rounded ${conditionColors[book.condition] || 'bg-gray-100'}">${book.condition.replace('-', ' ')}</span>
                                        <span class="inline-block px-2 py-1 text-xs font-semibold bg-gray-200 text-gray-800 rounded">€${parseFloat(book.price).toFixed(2)}</span>
                                        ${hasReclaim ? '<span class="inline-block px-2 py-1 text-xs font-semibold bg-yellow-100 text-yellow-800 rounded">IN RESO</span>' : ''}
                                    </div>
                                </div>
                                ${!hasReclaim ? `<button type="button" onclick="createReclaim(${book.id})" class="ml-4 px-3 py-1 bg-red-600 text-white text-sm font-medium rounded hover:bg-red-700 transition whitespace-nowrap">
                                    + Reso
                                </button>` : '<span class="ml-4 px-3 py-1 bg-gray-300 text-gray-700 text-sm font-medium rounded whitespace-nowrap">In Reso</span>'}
                            </div>
                        </div>
                    `;
                }).join('');

                buyerBooksBox.classList.remove('hidden');
            })
            .catch(error => {
                console.error('Errore nel caricamento libri:', error);
                alert('Errore nel caricamento dei libri: ' + error.message);
            });
    }

    function clearBuyerSearch() {
        buyerSearch.value = '';
        currentBuyerId = null;
        currentBuyerData = null;
        buyerResults.classList.add('hidden');
        buyerInfoBox.classList.add('hidden');
        buyerBooksBox.classList.add('hidden');
        initialMessage.classList.remove('hidden');
        buyerBooksList.innerHTML = '';
    }

    function createReclaim(bookListingId) {
        // Reindirizza alla pagina di creazione del reso
        window.location.href = `{{ route('staff.reclaims.create') }}?book_listing_id=${bookListingId}`;
    }
</script>
@endsection
