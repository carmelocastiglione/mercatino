@extends('layouts.app-staff')

@section('content')
    <div class="mb-8">
        <h1 class="text-4xl font-bold text-gray-900">{{ $title }}</h1>
        <p class="text-gray-600 mt-2">{{ $description }}</p>
    </div>

    <!-- Statistiche Resi -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <x-stats-card
            label="Numero di Resi Approvati"
            :value="$totalReclaims"
            color="red"
        />
        <x-stats-card
            label="Importo Restituito"
            :value="'€' . number_format($totalReclaimedAmount, 2, ',', '.')"
            color="red"
        />
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

    <!-- Lista libri elegibili per restituzione -->
    <div id="buyer_books_box" class="hidden">
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-900">
                Libri Elegibili per Restituzione <span id="books_count" class="ml-2 inline-block bg-purple-600 text-white text-sm font-bold rounded-full px-3 py-1">0</span>
            </h2>
        </div>

        <div id="buyer_books_list" class="divide-y divide-gray-200">
            <!-- Libri caricati via JS -->
        </div>
    </div>

    <!-- Messaggio iniziale -->
    <div id="initial_message" class="bg-white rounded-lg shadow-sm border border-gray-200 p-8 text-center">
        <p class="text-gray-500 text-lg">Cerca un acquirente per visualizzare i libri acquistati.</p>
        <p class="text-gray-500 text-lg">E' possibile restituire solamente un libro per cui non è stato ancora riscosso l'importo dal venditore.</p>
    </div>

    <!-- Lista di tutti i resi -->
    <div class="mt-12">
        <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center gap-2">
            Tutti i Resi
        </h2>

        <!-- Filter Form -->
        <div class="bg-white border border-red-200 rounded-lg p-6 mb-8">
            <form method="GET" action="{{ route('staff.reclaims.index') }}" class="flex gap-2">
                <input 
                    type="text" 
                    name="q" 
                    placeholder="Filtra per nome, cognome, email o codice venditore..." 
                    value="{{ $filterQuery }}"
                    class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
                    autocomplete="off"
                />
                <button type="submit" class="px-6 py-2 bg-red-600 text-white font-medium rounded-lg hover:bg-red-700 transition">
                    Filtra
                </button>
                @if($filterQuery)
                    <a href="{{ route('staff.reclaims.index') }}" class="px-6 py-2 bg-gray-500 text-white font-medium rounded-lg hover:bg-gray-600 transition">
                        Reset
                    </a>
                @endif
            </form>
            @if($filterQuery)
                <p class="text-sm text-gray-600 mt-2">Risultati per: <strong>{{ $filterQuery }}</strong> ({{ $reclaims->total() }} risultati)</p>
            @endif
        </div>

        @if($reclaims->isEmpty())
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-8 text-center">
                <p class="text-gray-500 text-lg">Nessun reso al momento.</p>
            </div>
        @else
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <table class="w-full">
                    <thead class="bg-gradient-to-r from-indigo-50 to-blue-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Libro</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Acquirente</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Venditore</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Data</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Stato</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Azione</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($reclaims as $reclaim)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4">
                                    <div>
                                        <p class="font-medium text-gray-900">{{ $reclaim->bookListing->book->title }}</p>
                                        <p class="text-sm text-gray-600 font-mono">{{ $reclaim->bookListing->book->isbn ?? 'N/A' }}</p>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div>
                                        <p class="font-medium text-gray-900">{{ $reclaim->buyer->name ?? 'N/A' }} {{ $reclaim->buyer->surname ?? '' }}</p>
                                        <p class="text-sm text-gray-600">{{ $reclaim->buyer->code ?? 'N/A' }}</p>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div>
                                        <p class="font-medium text-gray-900">{{ $reclaim->user->name ?? 'N/A' }} {{ $reclaim->user->surname ?? '' }}</p>
                                        <p class="text-sm text-gray-600">{{ $reclaim->user->code ?? 'N/A' }}</p>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-sm text-gray-600">{{ $reclaim->created_at->format('d/m/Y H:i') }}</p>
                                </td>
                                <td class="px-6 py-4">
                                    @php
                                        $statusColors = [
                                            'approved' => 'bg-green-100 text-green-800',
                                            'rejected' => 'bg-red-100 text-red-800',
                                            'pending' => 'bg-yellow-100 text-yellow-800',
                                        ];
                                        $statusLabels = [
                                            'approved' => 'Approvato',
                                            'rejected' => 'Rifiutato',
                                            'pending' => 'In Sospeso',
                                        ];
                                    @endphp
                                    <span class="inline-block px-3 py-1 text-sm font-semibold rounded-full {{ $statusColors[$reclaim->status] ?? 'bg-gray-100 text-gray-800' }}">
                                        {{ $statusLabels[$reclaim->status] ?? ucfirst($reclaim->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <a href="{{ route('staff.reclaims.show', $reclaim->id) }}" class="inline-block px-3 py-1 bg-blue-600 text-white text-sm font-medium rounded hover:bg-blue-700 transition">
                                        Visualizza
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4 border-t border-gray-200">
                {{ $reclaims->links() }}
            </div>
        @endif
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
                    buyerBooksList.innerHTML = '<div class="p-8 text-center text-gray-500">Questo acquirente non ha libri che possono essere restituiti</div>';
                    buyerBooksBox.classList.remove('hidden');
                    return;
                }

                const conditionColors = {
                    'like-new': 'bg-green-100 text-green-800',
                    'good': 'bg-blue-100 text-blue-800',
                    'fair': 'bg-yellow-100 text-yellow-800',
                    'poor': 'bg-red-100 text-red-800'
                };

                const conditionLabels = {
                    'like-new': 'Come Nuovo',
                    'good': 'Buono',
                    'fair': 'Discreto',
                    'poor': 'Scadente'
                };

                buyerBooksList.innerHTML = buyerBooks.map((book) => {
                    return `
                        <div class="p-4 bg-white hover:bg-gray-50 transition border-b border-gray-100">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <p class="font-medium text-gray-900">${book.title}</p>
                                    <p class="text-sm text-gray-600">${book.author || 'Autore sconosciuto'}</p>
                                    ${book.isbn ? `<p class="text-sm text-gray-500">ISBN: ${book.isbn}</p>` : ''}
                                    <div class="flex gap-2 mt-2">
                                        <span class="inline-block px-2 py-1 text-xs font-semibold rounded ${conditionColors[book.condition] || 'bg-gray-100'}">${conditionLabels[book.condition] || book.condition}</span>
                                        <span class="inline-block px-2 py-1 text-xs font-semibold bg-gray-200 text-gray-800 rounded">€${book.price_sell ? parseFloat(book.price_sell).toFixed(2) : '0.00'}</span>
                                    </div>
                                </div>
                                <button type="button" onclick="createReclaim(${book.id})" class="ml-4 px-3 py-1 bg-red-600 text-white text-sm font-medium rounded hover:bg-red-700 transition whitespace-nowrap">
                                    Inizia Reso
                                </button>
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
