@extends('layouts.app-staff')

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="px-4 py-6 sm:px-0">
        <!-- Header -->
        <h1 class="text-3xl font-bold text-gray-900 mb-6">Gestione Riscossioni</h1>

        <!-- Search Form -->
        <div class="bg-white shadow-md rounded-lg p-6 mb-6">
            <label for="seller-search" class="block text-sm font-semibold text-gray-900 mb-2">
                Seleziona Venditore
            </label>
            <div class="relative">
                <input type="text" id="seller-search" placeholder="Cerca per nome, cognome, codice o email..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-indigo-500">
                <div id="search-results" class="hidden absolute top-full left-0 right-0 mt-2 bg-white border border-gray-300 rounded-lg shadow-lg z-10 max-h-64 overflow-y-auto">
                    <!-- Results will be populated by JavaScript -->
                </div>
            </div>
        </div>

        <!-- No Seller Selected -->
        <div id="no-seller" class="bg-blue-50 border border-blue-200 rounded-lg p-6 text-center">
            <p class="text-gray-600 text-lg">Seleziona un venditore dalla ricerca per visualizzare i dettagli e gestire le riscossioni.</p>
        </div>

        <!-- Seller Details Section (Hidden until seller is selected) -->
        <div id="seller-details" class="hidden">
            <!-- Summary Cards -->
            <div class="grid grid-cols-3 gap-4 mb-6">
                <div class="bg-green-100 border-l-4 border-green-600 rounded-lg p-4">
                    <p class="text-sm text-gray-600 uppercase tracking-wide">Totale Vendite</p>
                    <p class="text-3xl font-bold text-green-600" id="total-sales-amount">0€</p>
                </div>
                <div class="bg-red-100 border-l-4 border-red-600 rounded-lg p-4">
                    <p class="text-sm text-gray-600 uppercase tracking-wide">Già Riscosso</p>
                    <p class="text-3xl font-bold text-red-600" id="already-withdrawn-amount">0€</p>
                </div>
                <div class="bg-blue-100 border-l-4 border-blue-600 rounded-lg p-4">
                    <p class="text-sm text-gray-600 uppercase tracking-wide">Da Ritirare</p>
                    <p class="text-3xl font-bold text-blue-600" id="available-amount">0€</p>
                </div>
            </div>

            <!-- Books Tabs -->
            <div class="bg-white shadow-md rounded-lg mb-6">
                <!-- Tabs Navigation -->
                <div class="flex border-b border-gray-200">
                    <button class="tab-btn flex-1 py-4 px-6 text-center font-semibold border-b-2 border-indigo-600 text-indigo-600" data-tab="sold-books">
                        Libri Venduti
                    </button>
                    <button class="tab-btn flex-1 py-4 px-6 text-center font-semibold border-b-2 border-transparent text-gray-600 hover:text-gray-900" data-tab="unsold-books">
                        Libri Non Venduti
                    </button>
                </div>

                <!-- Sold Books Tab -->
                <div id="sold-books-content" class="tab-content p-6">
                    <table class="w-full" id="sold-books-table">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-4 py-2 text-left text-sm font-semibold text-gray-900">Titolo</th>
                                <th class="px-4 py-2 text-left text-sm font-semibold text-gray-900">Autore</th>
                                <th class="px-4 py-2 text-center text-sm font-semibold text-gray-900">Condizione</th>
                                <th class="px-4 py-2 text-right text-sm font-semibold text-gray-900">Prezzo</th>
                                <th class="px-4 py-2 text-center text-sm font-semibold text-gray-900">Azioni</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200" id="sold-books-body">
                            <!-- Populated by JavaScript -->
                        </tbody>
                    </table>
                    <p id="no-sold-books" class="text-center text-gray-500 py-4">Nessun libro venduto</p>
                </div>

                <!-- Unsold Books Tab -->
                <div id="unsold-books-content" class="tab-content p-6 hidden">
                    <table class="w-full" id="unsold-books-table">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-4 py-2 text-left text-sm font-semibold text-gray-900">Titolo</th>
                                <th class="px-4 py-2 text-left text-sm font-semibold text-gray-900">Autore</th>
                                <th class="px-4 py-2 text-center text-sm font-semibold text-gray-900">Condizione</th>
                                <th class="px-4 py-2 text-center text-sm font-semibold text-gray-900">Stato</th>
                                <th class="px-4 py-2 text-right text-sm font-semibold text-gray-900">Prezzo</th>
                                <th class="px-4 py-2 text-center text-sm font-semibold text-gray-900">Azioni</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200" id="unsold-books-body">
                            <!-- Populated by JavaScript -->
                        </tbody>
                    </table>
                    <p id="no-unsold-books" class="text-center text-gray-500 py-4">Nessun libro non venduto</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let selectedSeller = null;
let debounceTimer;

// Search sellers
document.getElementById('seller-search').addEventListener('input', function(e) {
    clearTimeout(debounceTimer);
    const query = e.target.value;

    if (query.length < 2) {
        document.getElementById('search-results').classList.add('hidden');
        return;
    }

    debounceTimer = setTimeout(() => {
        fetch(`{{ route('staff.withdrawals.search-sellers') }}?q=${encodeURIComponent(query)}`)
            .then(response => response.json())
            .then(data => {
                const resultsDiv = document.getElementById('search-results');
                resultsDiv.innerHTML = '';

                if (data.length === 0) {
                    resultsDiv.innerHTML = '<div class="px-4 py-2 text-gray-500">Nessun venditore trovato</div>';
                    resultsDiv.classList.remove('hidden');
                    return;
                }

                data.forEach(seller => {
                    const div = document.createElement('div');
                    div.className = 'px-4 py-2 hover:bg-indigo-50 cursor-pointer border-b border-gray-200';
                    div.innerHTML = `<strong>${seller.name} ${seller.surname}</strong><br><small class="text-gray-600">${seller.email} (${seller.code})</small>`;
                    div.onclick = () => selectSeller(seller.id, seller.name, seller.surname);
                    resultsDiv.appendChild(div);
                });

                resultsDiv.classList.remove('hidden');
            });
    }, 300);
});

// Select seller
function selectSeller(sellerId, sellerName, sellerSurname) {
    // Hide search results
    document.getElementById('search-results').classList.add('hidden');
    document.getElementById('seller-search').value = `${sellerName} ${sellerSurname}`;

    // Fetch seller details
    fetch(`{{ route('staff.withdrawals.process-seller', ['user' => ':id']) }}`.replace(':id', sellerId))
        .then(response => response.text())
        .then(html => {
            // Extract the data from the HTML response
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            
            // Extract sold books data
            const soldBooksJson = doc.querySelector('[data-sold-books]')?.textContent;
            const unsoldBooksJson = doc.querySelector('[data-unsold-books]')?.textContent;
            const sellerDataJson = doc.querySelector('[data-seller]')?.textContent;

            if (soldBooksJson && unsoldBooksJson && sellerDataJson) {
                const soldBooks = JSON.parse(soldBooksJson);
                const unsoldBooks = JSON.parse(unsoldBooksJson);
                const sellerData = JSON.parse(sellerDataJson);

                displaySellerDetails(sellerData, soldBooks, unsoldBooks);
            }
        });
}

// Display seller details
function displaySellerDetails(seller, soldBooks, unsoldBooks) {
    // Update summary cards
    document.getElementById('total-sales-amount').textContent = formatCurrency(seller.total_sales);
    document.getElementById('already-withdrawn-amount').textContent = formatCurrency(seller.total_withdrawn);
    document.getElementById('available-amount').textContent = formatCurrency(seller.available_balance);

    // Display sold books
    const soldBooksBody = document.getElementById('sold-books-body');
    const noSoldBooks = document.getElementById('no-sold-books');
    
    if (soldBooks.length === 0) {
        soldBooksBody.innerHTML = '';
        noSoldBooks.classList.remove('hidden');
    } else {
        noSoldBooks.classList.add('hidden');
        soldBooksBody.innerHTML = soldBooks.map(book => `
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3 text-sm text-gray-900"><strong>${book.title}</strong></td>
                <td class="px-4 py-3 text-sm text-gray-600">${book.author}</td>
                <td class="px-4 py-3 text-center text-sm">
                    <span class="px-2 py-1 rounded text-white text-xs font-semibold ${getConditionColor(book.condition)}">
                        ${getConditionLabel(book.condition)}
                    </span>
                </td>
                <td class="px-4 py-3 text-right text-sm text-gray-900"><strong>${formatCurrency(book.price)}</strong></td>
                <td class="px-4 py-3 text-center">
                    <form action="{{ route('staff.withdrawals.withdraw-money', ['listing' => ':id']) }}".replace(':id', book.id) method="POST" style="display:inline;">
                        @csrf
                        <button type="submit" class="bg-green-600 hover:bg-green-700 text-white text-xs font-bold py-1 px-3 rounded">
                            Ritira Soldi
                        </button>
                    </form>
                </td>
            </tr>
        `).join('');
    }

    // Display unsold books
    const unsoldBooksBody = document.getElementById('unsold-books-body');
    const noUnsoldBooks = document.getElementById('no-unsold-books');
    
    if (unsoldBooks.length === 0) {
        unsoldBooksBody.innerHTML = '';
        noUnsoldBooks.classList.remove('hidden');
    } else {
        noUnsoldBooks.classList.add('hidden');
        unsoldBooksBody.innerHTML = unsoldBooks.map(book => `
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3 text-sm text-gray-900"><strong>${book.title}</strong></td>
                <td class="px-4 py-3 text-sm text-gray-600">${book.author}</td>
                <td class="px-4 py-3 text-center text-sm">
                    <span class="px-2 py-1 rounded text-white text-xs font-semibold ${getConditionColor(book.condition)}">
                        ${getConditionLabel(book.condition)}
                    </span>
                </td>
                <td class="px-4 py-3 text-center text-sm">
                    <span class="px-2 py-1 rounded text-white text-xs font-semibold ${getStatusColor(book.status)}">
                        ${getStatusLabel(book.status)}
                    </span>
                </td>
                <td class="px-4 py-3 text-right text-sm text-gray-900"><strong>${formatCurrency(book.price)}</strong></td>
                <td class="px-4 py-3 text-center">
                    <form action="{{ route('staff.withdrawals.withdraw-book', ['listing' => ':id']) }}".replace(':id', book.id) method="POST" style="display:inline;">
                        @csrf
                        <button type="submit" class="bg-orange-600 hover:bg-orange-700 text-white text-xs font-bold py-1 px-3 rounded" onclick="return confirm('Sei sicuro di voler ritirare questo libro?')">
                            Ritira Libro
                        </button>
                    </form>
                </td>
            </tr>
        `).join('');
    }

    // Show seller details section
    document.getElementById('no-seller').classList.add('hidden');
    document.getElementById('seller-details').classList.remove('hidden');
}

// Helper functions
function formatCurrency(value) {
    return parseFloat(value).toFixed(2).replace('.', ',') + '€';
}

function getConditionColor(condition) {
    const colors = {
        'excellent': 'bg-green-600',
        'good': 'bg-blue-600',
        'fair': 'bg-yellow-600',
        'poor': 'bg-red-600'
    };
    return colors[condition] || 'bg-gray-600';
}

function getConditionLabel(condition) {
    const labels = {
        'excellent': 'Ottima',
        'good': 'Buona',
        'fair': 'Discreta',
        'poor': 'Cattiva'
    };
    return labels[condition] || condition;
}

function getStatusColor(status) {
    const colors = {
        'available': 'bg-blue-600',
        'pending': 'bg-yellow-600',
        'withdrawn': 'bg-red-600'
    };
    return colors[status] || 'bg-gray-600';
}

function getStatusLabel(status) {
    const labels = {
        'available': 'Disponibile',
        'pending': 'In Sospeso',
        'withdrawn': 'Ritirato'
    };
    return labels[status] || status;
}

// Tab switching
document.querySelectorAll('.tab-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const tabName = this.getAttribute('data-tab');
        
        // Hide all tabs
        document.querySelectorAll('.tab-content').forEach(content => {
            content.classList.add('hidden');
        });

        // Remove active class from all buttons
        document.querySelectorAll('.tab-btn').forEach(b => {
            b.classList.remove('border-indigo-600', 'text-indigo-600');
            b.classList.add('border-transparent', 'text-gray-600');
        });

        // Show selected tab
        document.getElementById(tabName + '-content').classList.remove('hidden');

        // Add active class to button
        this.classList.remove('border-transparent', 'text-gray-600');
        this.classList.add('border-indigo-600', 'text-indigo-600');
    });
});
</script>
@endsection
