@extends('layouts.app-staff')

@section('title', 'Nuova Vendita')

@section('content')
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-4xl font-bold text-gray-900">Registra Vendite</h1>
            <p class="text-gray-600 mt-2">Vendi più libri in una sola operazione</p>
        </div>
        <a href="{{ route('staff.sales.index') }}" class="px-6 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition font-medium">
            ← Torna alle vendite
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Left Column: Form -->
        <div class="lg:col-span-2">
            <div class="space-y-6">
                <!-- Buyer Selection -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <label for="buyer_search" class="block text-sm font-semibold text-gray-900 mb-2">
                        Acquirente <span class="text-red-600">*</span>
                    </label>
                    <div class="relative">
                        <input 
                            type="text" 
                            id="buyer_search" 
                            placeholder="Cerca per nome, codice o email..."
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                            autocomplete="off"
                        >
                        <div id="buyer_results" class="absolute top-full left-0 right-0 mt-1 bg-white border border-gray-300 rounded-lg shadow-lg hidden max-h-64 overflow-y-auto z-10"></div>
                    </div>
                    <div id="buyer_selected" class="mt-4 hidden">
                        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                            <p class="text-sm text-gray-600">Acquirente selezionato:</p>
                            <p id="buyer_name" class="text-lg font-bold text-gray-900"></p>
                            <p id="buyer_code" class="text-sm text-gray-600"></p>
                        </div>
                    </div>
                    <input type="hidden" id="buyer_id" value="">
                </div>

                <!-- Book Search & Selection -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <label for="book_search" class="block text-sm font-semibold text-gray-900 mb-2">
                        Libro <span class="text-red-600">*</span>
                    </label>
                    <div class="relative">
                        <input 
                            type="text" 
                            id="book_search" 
                            placeholder="Cerca titolo, autore o ISBN..."
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                            autocomplete="off"
                        >
                        <div id="book_results" class="absolute top-full left-0 right-0 mt-1 bg-white border border-gray-300 rounded-lg shadow-lg hidden max-h-64 overflow-y-auto z-10"></div>
                    </div>
                    <input type="hidden" id="book_listing_id" value="">
                </div>

                <!-- Add to Cart Button -->
                <button type="button" onclick="addToCart()" class="w-full px-6 py-3 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition">
                    ➕ Aggiungi Libro
                </button>
            </div>
        </div>

        <!-- Right Column: Sticky Sidebar -->
        <div class="lg:col-span-1">
            <div class="sticky top-8 bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <!-- Buyer Info Box -->
                <div id="buyer_info_box" class="mb-6 hidden">
                    <p class="text-sm text-gray-600 mb-2">Acquirente</p>
                    <div class="bg-gradient-to-r from-green-50 to-green-100 border-2 border-green-300 rounded-lg px-4 py-3">
                        <p id="sidebar_buyer_code" class="text-2xl font-bold text-green-600">--</p>
                        <p id="sidebar_buyer_name" class="text-xs text-gray-600 mt-1"></p>
                    </div>
                </div>

                <!-- Cart Summary Header -->
                <div class="mb-4">
                    <h2 class="text-lg font-bold text-gray-900">Riepilogo</h2>
                    <span id="cart_count" class="inline-block bg-green-600 text-white px-3 py-1 rounded-full text-sm font-bold mt-2">0</span>
                </div>

                <!-- Cart Items -->
                <div id="cart_items" class="space-y-3 mb-6 max-h-96 overflow-y-auto border-t border-gray-200 pt-4">
                    <!-- Cart items will be inserted here -->
                </div>

                <!-- Total -->
                <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-4">
                    <p class="text-sm text-gray-600">Totale Vendite</p>
                    <p id="cart_total" class="text-3xl font-bold text-green-600">€0,00</p>
                </div>

                <!-- Action Buttons -->
                <div class="space-y-3">
                    <button type="button" onclick="finishSales()" class="w-full px-6 py-3 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition">
                        ✓ Salva Vendite
                    </button>
                    <button type="button" onclick="clearCart()" class="w-full px-6 py-3 bg-red-600 text-white font-medium rounded-lg hover:bg-red-700 transition">
                        🗑️ Cancella Tutto
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-8"></div>

    <script>
        let cart = [];
        let selectedBuyer = null;

        // Buyer Search with Debounce
        let buyerDebounce;
        document.getElementById('buyer_search').addEventListener('input', function(e) {
            clearTimeout(buyerDebounce);
            const query = e.target.value.trim();
            
            if (query.length < 2) {
                document.getElementById('buyer_results').classList.add('hidden');
                return;
            }

            buyerDebounce = setTimeout(() => {
                fetch(`{{ route('staff.sales.search-buyers') }}?q=${encodeURIComponent(query)}`)
                    .then(r => r.json())
                    .then(data => {
                        const resultsDiv = document.getElementById('buyer_results');
                        if (data.length === 0) {
                            resultsDiv.innerHTML = '<div class="p-3 text-gray-500 text-sm">Nessun acquirente trovato</div>';
                        } else {
                            resultsDiv.innerHTML = data.map(buyer => `
                                <div class="p-3 hover:bg-green-50 cursor-pointer border-b border-gray-100 last:border-b-0" onclick="selectBuyer(${buyer.id}, '${buyer.name}', '${buyer.surname}', '${buyer.code}', '${buyer.email}')">
                                    <p class="font-medium text-sm text-gray-900">${buyer.name} ${buyer.surname}</p>
                                    <p class="text-xs text-gray-600">${buyer.code} • ${buyer.email}</p>
                                </div>
                            `).join('');
                        }
                        resultsDiv.classList.remove('hidden');
                    });
            }, 300);
        });

        function selectBuyer(id, name, surname, code, email) {
            selectedBuyer = { id, name, surname, code, email };
            document.getElementById('buyer_id').value = id;
            document.getElementById('buyer_search').value = `${name} ${surname} (${code})`;
            document.getElementById('buyer_results').classList.add('hidden');

            // Show selected buyer info
            const selectedDiv = document.getElementById('buyer_selected');
            document.getElementById('buyer_name').textContent = `${name} ${surname}`;
            document.getElementById('buyer_code').textContent = code;
            selectedDiv.classList.remove('hidden');

            // Update sidebar
            document.getElementById('buyer_info_box').classList.remove('hidden');
            document.getElementById('sidebar_buyer_code').textContent = code;
            document.getElementById('sidebar_buyer_name').textContent = `${name} ${surname}`;
        }

        // Book Search with Debounce
        let bookDebounce;
        document.getElementById('book_search').addEventListener('input', function(e) {
            clearTimeout(bookDebounce);
            const query = e.target.value.trim();
            
            if (query.length < 2) {
                document.getElementById('book_results').classList.add('hidden');
                return;
            }

            bookDebounce = setTimeout(() => {
                fetch(`{{ route('staff.sales.search-listings') }}?q=${encodeURIComponent(query)}`)
                    .then(r => r.json())
                    .then(data => {
                        const resultsDiv = document.getElementById('book_results');
                        if (data.length === 0) {
                            resultsDiv.innerHTML = '<div class="p-3 text-gray-500 text-sm">Nessun libro disponibile</div>';
                        } else {
                            resultsDiv.innerHTML = data.map(book => `
                                <div class="p-3 hover:bg-green-50 cursor-pointer border-b border-gray-100 last:border-b-0" onclick="selectBook(${book.id}, '${book.title}', '${book.author}', ${book.price}, '${book.condition}')">
                                    <p class="font-medium text-sm text-gray-900">${book.title}</p>
                                    <p class="text-xs text-gray-600">${book.author}</p>
                                    <p class="text-xs text-gray-600 mt-1">Condizione: <span class="font-semibold">${book.condition}</span></p>
                                    <p class="text-xs text-gray-600">Venditore: <span class="font-semibold">${book.seller_name} ${book.seller_surname}</span> (${book.seller_code})</p>
                                    <p class="text-xs text-green-600 font-semibold mt-1">€${parseFloat(book.price).toFixed(2)}</p>
                                </div>
                            `).join('');
                        }
                        resultsDiv.classList.remove('hidden');
                    });
            }, 300);
        });

        function selectBook(id, title, author, price, condition) {
            document.getElementById('book_listing_id').value = id;
            document.getElementById('book_search').value = `${title} - ${author}`;
            document.getElementById('book_results').classList.add('hidden');
        }

        // Add to Cart
        function addToCart() {
            const buyerId = document.getElementById('buyer_id').value;
            const listingId = document.getElementById('book_listing_id').value;

            if (!buyerId) {
                showToast('Seleziona un acquirente', 'error');
                return;
            }
            if (!listingId) {
                showToast('Seleziona un libro', 'error');
                return;
            }

            const bookTitle = document.getElementById('book_search').value.split(' - ')[0];
            const bookPrice = parseFloat(document.querySelector('[data-price]')?.dataset.price || 0);

            cart.push({
                buyer_id: parseInt(buyerId),
                book_listing_id: parseInt(listingId),
                title: bookTitle,
            });

            updateCartDisplay();
            resetForm();
            showToast('✓ Libro aggiunto al carrello', 'success');
        }

        function removeFromCart(index) {
            cart.splice(index, 1);
            updateCartDisplay();
        }

        function clearCart() {
            if (cart.length === 0) {
                showToast('Carrello vuoto', 'info');
                return;
            }
            if (confirm('Cancellare tutti gli articoli dal carrello?')) {
                cart = [];
                updateCartDisplay();
                showToast('Carrello svuotato', 'info');
            }
        }

        function updateCartDisplay() {
            const counter = document.getElementById('cart_count');
            const itemsDiv = document.getElementById('cart_items');
            const totalDiv = document.getElementById('cart_total');

            counter.textContent = cart.length;

            if (cart.length === 0) {
                itemsDiv.innerHTML = '<p class="text-gray-500 text-sm text-center py-4">Nessun libro nel carrello</p>';
                totalDiv.textContent = '€0,00';
                return;
            }

            itemsDiv.innerHTML = cart.map((item, index) => `
                <div class="flex items-start justify-between p-3 bg-gray-50 rounded-lg">
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-900">${item.title}</p>
                    </div>
                    <button onclick="removeFromCart(${index})" class="text-red-600 hover:text-red-800 font-bold text-lg ml-2">×</button>
                </div>
            `).join('');

            totalDiv.textContent = '€' + (cart.reduce((sum, item) => sum + 0, 0)).toFixed(2);
        }

        function resetForm() {
            document.getElementById('book_search').value = '';
            document.getElementById('book_listing_id').value = '';
        }

        function showToast(message, type = 'info') {
            const toast = document.createElement('div');
            toast.className = `fixed bottom-4 right-4 px-6 py-3 rounded-lg text-white font-medium z-50 ${
                type === 'success' ? 'bg-green-600' :
                type === 'error' ? 'bg-red-600' :
                'bg-blue-600'
            }`;
            toast.textContent = message;
            document.body.appendChild(toast);
            setTimeout(() => toast.remove(), 3000);
        }

        async function finishSales() {
            if (cart.length === 0) {
                showToast('Aggiungi almeno un libro', 'error');
                return;
            }
            if (!confirm(`Stai per registrare ${cart.length} vendita${cart.length !== 1 ? 'e' : ''}. Continuare?`)) {
                return;
            }
            try {
                const response = await fetch('{{ route("staff.sales.store-batch") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-Token': document.querySelector('input[name="_token"]')?.value || '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        sales: cart
                    })
                });
                const data = await response.json();
                if (!response.ok) {
                    showToast('Errore: ' + (data.message || 'Errore durante il salvataggio'), 'error');
                    return;
                }
                showToast(`✓ ${data.message}`, 'success');
                window.location.href = `{{ route('staff.sales.show', ':id') }}`.replace(':id', data.sale_id);
            } catch (error) {
                console.error('Errore:', error);
                showToast('Errore durante il salvataggio: ' + error.message, 'error');
            }
        }
    </script>

    <!-- CSRF Token (hidden) -->
    @csrf
@endsection
