@extends('layouts.app-staff')

@section('title', 'Nuova Vendita')

@section('content')


    <div class="mb-8">
        <div class="flex items-center space-x-4 mb-6">
            <a href="{{ route('staff.sales.index') }}" class="text-blue-600 hover:text-blue-800 font-medium">← Torna alle vendite</a>
        </div>
        <h1 class="text-4xl font-bold text-gray-900">Nuova Vendita</h1>
        <p class="text-gray-600 mt-2">Aggiungi uno o più libri per creare una nuova vendita</p>
    </div>

    {{-- Pre-populated message if coming from book reservations --}}
    @if(!empty($approvedReservations) && count($approvedReservations) > 0)
        <div class="mb-6 bg-green-50 border border-green-300 rounded-lg p-4">
            <p class="text-green-800 font-medium">✓ {{ count($approvedReservations) }} libro/i caricato/i dalla prenotazione</p>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Left Column: Form -->
        <div class="lg:col-span-2">
            <div class="space-y-6">
                <!-- Buyer Password Box (shown only when online sales are enabled) -->
                @if($enableOnlineSales)
                    <div id="buyer_password_box" class="hidden bg-gradient-to-r from-green-50 to-green-100 border-2 border-green-300 rounded-lg p-6 shadow-md">
                        <p class="text-lg text-center text-gray-600 mb-4">Credenziali di accesso</p>
                        <p class="text-sm text-center text-gray-600 mb-4">Comunica queste credenziali al venditore per accedere al portale online. Queste credenziali vengono visualizzate una sola volta e verranno stampate nella ricevuta successiva.</p>
                        <div class="space-y-4">
                            <div>
                                <p class="text-xs text-center text-gray-600 mb-2">Email:</p>
                                <p id="buyer_email_display" class="font-mono bg-white px-4 py-3 rounded border border-green-200 text-green-700 text-center text-lg break-all"></p>
                            </div>
                            <div>
                                <p class="text-xs text-center text-gray-600 mb-2">Password:</p>
                                <code id="buyer_password_display" class="font-mono bg-white px-4 py-3 rounded border border-green-200 text-green-700 block text-center text-lg"></code>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Info Box: Multiple Books Selection -->
                <x-info-box
                    type="info"
                    title="Selezione di più libri"
                    message="Puoi selezionare uno o più libri da vendere da questo modulo. Aggiungi ogni libro utilizzando il modulo sottostante e al termine inviali insieme per un'unica vendita."
                />

                <!-- Buyer Selection -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <label for="buyer_search" class="block text-sm font-semibold text-gray-900 mb-2">
                        Acquirente <span class="text-red-600">*</span>
                    </label>
                    <div class="flex gap-2">
                        <div class="relative flex-1">
                            <input 
                                type="text" 
                                id="buyer_search" 
                                placeholder="Cerca per cognome, email o codice..."
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                autocomplete="off"
                            >
                            <div id="buyer_results" class="absolute top-full left-0 right-0 mt-1 bg-white border border-gray-300 rounded-lg shadow-lg hidden max-h-64 overflow-y-auto z-10"></div>
                        </div>
                        <button type="button" onclick="openRegisterModal()" class="px-4 py-2 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition whitespace-nowrap">
                            + Aggiungi
                        </button>
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
                    <h3 class="text-sm font-semibold text-gray-900 mb-4">Ricerca Libri</h3>
                    
                    <!-- Two-column search filters -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label for="book_title_search" class="block text-sm font-medium text-gray-700 mb-2">
                                Libro
                            </label>
                            <input 
                                type="text" 
                                id="book_title_search" 
                                placeholder="Cerca titolo, autore o ISBN..."
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                autocomplete="off"
                            >
                        </div>
                        <div>
                            <label for="seller_code_search" class="block text-sm font-medium text-gray-700 mb-2">
                                Venditore
                            </label>
                            <input 
                                type="text" 
                                id="seller_code_search" 
                                placeholder="Cerca codice venditore..."
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                autocomplete="off"
                            >
                        </div>
                    </div>

                    <!-- Results Table -->
                    <div id="book_results_container" class="hidden">
                        <div class="overflow-x-auto border border-gray-200 rounded-lg">
                            <table class="w-full text-sm">
                                <thead class="bg-gray-50 border-b border-gray-200">
                                    <tr>
                                        <th class="px-4 py-3 text-left font-semibold text-gray-700">Titolo</th>
                                        <th class="px-4 py-3 text-left font-semibold text-gray-700">ISBN</th>
                                        <th class="px-4 py-3 text-left font-semibold text-gray-700">Venditore</th>
                                        <th class="px-4 py-3 text-left font-semibold text-gray-700">Condizione</th>
                                        <th class="px-4 py-3 text-right font-semibold text-gray-700">Prezzo</th>
                                        <th class="px-4 py-3 text-center font-semibold text-gray-700">Azione</th>
                                    </tr>
                                </thead>
                                <tbody id="book_results_table" class="divide-y divide-gray-200">
                                    <!-- Results will be inserted here -->
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- No results message -->
                    <div id="book_no_results" class="hidden text-center py-6">
                        <p class="text-gray-500">Nessun libro trovato. Prova a modificare i filtri di ricerca.</p>
                    </div>

                    <!-- Empty state -->
                    <div id="book_search_empty" class="text-center py-6">
                        <p class="text-gray-500">Inserisci i criteri di ricerca per visualizzare i libri disponibili.</p>
                    </div>

                    <input type="hidden" id="book_listing_id" value="">
                </div>

                <!-- Actions -->
                <div class="flex items-center justify-end gap-4">
                    <a href="{{ route('staff.sales.index') }}" class="px-6 py-3 text-gray-700 font-medium rounded-lg hover:bg-gray-100 transition">
                        Annulla
                    </a>
                </div>
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
                <div class="mb-4 flex items-center justify-between">
                    <h2 class="text-lg font-bold text-gray-900">Riepilogo</h2>
                    <span id="cart_count" class="inline-block bg-green-600 text-white px-3 py-1 rounded-full text-sm font-bold">0</span>
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
                        Salva Vendite
                    </button>
                    <button type="button" onclick="clearCart()" class="w-full px-6 py-3 bg-red-600 text-white font-medium rounded-lg hover:bg-red-700 transition">
                        Cancella Tutto
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-8"></div>

    <script>
        // Variables
        let cart = [];
        let selectedBuyer = null;
        let selectedBookPrice = 0;
        let isSaving = false;
        let currentBuyerPassword = null; // Memorizza password dell'acquirente

        // Global Functions (callable from onclick)
        function selectBuyer(id, name, surname, code, email, password) {
            selectedBuyer = { id, name, surname, code, email };
            document.getElementById('buyer_id').value = id;
            document.getElementById('buyer_search').value = `${name} ${surname} (${code})`;
            document.getElementById('buyer_results').classList.add('hidden');
            
            // Salva la password temporaneamente per inviarla al server
            currentBuyerPassword = password;

            // Show selected buyer info
            const selectedDiv = document.getElementById('buyer_selected');
            document.getElementById('buyer_name').textContent = `${name} ${surname}`;
            document.getElementById('buyer_code').textContent = code;
            selectedDiv.classList.remove('hidden');

            // Update sidebar
            document.getElementById('buyer_info_box').classList.remove('hidden');
            document.getElementById('sidebar_buyer_code').textContent = code;
            document.getElementById('sidebar_buyer_name').textContent = `${name} ${surname}`;

            // Nascondi le credenziali di default (se elemento esiste)
            const passwordBox = document.getElementById('buyer_password_box');
            if (passwordBox) {
                passwordBox.classList.add('hidden');
                
                if (password) {
                    document.getElementById('buyer_email_display').textContent = email;
                    document.getElementById('buyer_password_display').textContent = password;
                    passwordBox.classList.remove('hidden');
                }
            }
        }

        function selectBook(id, title, isbn, price, condition, sellerCode) {
            const buyerId = document.getElementById('buyer_id').value;

            if (!buyerId) {
                showToast('Seleziona prima un acquirente', 'error');
                return;
            }

            // Add directly to cart
            cart.push({
                buyer_id: parseInt(buyerId),
                book_listing_id: parseInt(id),
                title: title,
                isbn: isbn,
                condition: condition,
                price: parseFloat(price),
                seller_code: sellerCode,
            });

            updateCartDisplay();
            resetForm();
            showToast(`✓ ${title} aggiunto al carrello`, 'success');
        }

        function clearBookSelection() {
            document.getElementById('book_listing_id').value = '';
            document.getElementById('book_title_search').value = '';
            document.getElementById('seller_code_search').value = '';
            document.getElementById('book_results_container').classList.add('hidden');
            document.getElementById('book_no_results').classList.add('hidden');
            document.getElementById('book_search_empty').classList.remove('hidden');
        }

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

            cart.push({
                buyer_id: parseInt(buyerId),
                book_listing_id: parseInt(listingId),
                title: bookTitle,
                price: selectedBookPrice,
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

            const conditionLabels = {
                'like-new': 'Come Nuovo',
                'good': 'Buona',
                'fair': 'Discreta',
                'poor': 'Scarsa'
            };

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
                        <p class="text-xs text-gray-600">ISBN: <span class="font-mono">${item.isbn || 'N/A'}</span></p>
                        <p class="text-xs text-gray-600 mt-1">Venditore: <span class="font-bold">${item.seller_code || 'N/A'}</span></p>
                        <p class="text-xs text-gray-600">Condizione: <span class="font-normal">${conditionLabels[item.condition] || item.condition || 'N/A'}</span></p>
                    </div>
                    <div class="flex flex-col items-end gap-2 ml-2">
                        <p class="text-base font-bold text-green-600">€${parseFloat(item.price).toFixed(2)}</p>
                        <button onclick="removeFromCart(${index})" class="text-red-600 hover:text-red-800 font-bold text-lg">×</button>
                    </div>
                </div>
            `).join('');

            totalDiv.textContent = '€' + (cart.reduce((sum, item) => sum + (item.price || 0), 0)).toFixed(2);
        }

        function resetForm() {
            document.getElementById('book_title_search').value = '';
            document.getElementById('seller_code_search').value = '';
            document.getElementById('book_listing_id').value = '';
            document.getElementById('book_results_container').classList.add('hidden');
            document.getElementById('book_no_results').classList.add('hidden');
            document.getElementById('book_search_empty').classList.remove('hidden');
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
            
            // Prevent multiple submissions
            if (isSaving) {
                return;
            }
            
            isSaving = true;
            const saveButton = document.querySelector('button[onclick="finishSales()"]');
            saveButton.disabled = true;
            saveButton.style.opacity = '0.5';
            saveButton.style.cursor = 'not-allowed';
            
            try {
                const response = await fetch('{{ route("staff.sales.store-batch") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-Token': document.querySelector('input[name="_token"]')?.value || '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        buyer_password: currentBuyerPassword, // Invia password al server
                        sales: cart
                    })
                });
                const data = await response.json();
                if (!response.ok) {
                    showToast('Errore: ' + (data.message || 'Errore durante il salvataggio'), 'error');
                    isSaving = false;
                    saveButton.disabled = false;
                    saveButton.style.opacity = '1';
                    saveButton.style.cursor = 'pointer';
                    return;
                }
                showToast(`✓ ${data.message}`, 'success');
                window.location.href = data.redirect;
            } catch (error) {
                showToast('Errore durante il salvataggio: ' + error.message, 'error');
                isSaving = false;
                saveButton.disabled = false;
                saveButton.style.opacity = '1';
                saveButton.style.cursor = 'pointer';
            }
        }

        function openRegisterModal() {
            const registerModal = document.getElementById('register_modal');
            registerModal.classList.remove('hidden');
            document.getElementById('reg_name').focus();
        }

        function closeRegisterModal() {
            const registerModal = document.getElementById('register_modal');
            const registerForm = document.getElementById('register_form');
            registerModal.classList.add('hidden');
            registerForm.reset();
            clearRegisterErrors();
        }

        function clearRegisterErrors() {
            document.getElementById('reg_name_error').classList.add('hidden');
            document.getElementById('reg_surname_error').classList.add('hidden');
            document.getElementById('reg_email_error').classList.add('hidden');
            document.getElementById('register_message').classList.add('hidden');
        }

        // Initialize after DOM is ready
        document.addEventListener('DOMContentLoaded', function() {
            // Modal Management
            const registerModal = document.getElementById('register_modal');
            const registerForm = document.getElementById('register_form');

        registerForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            clearRegisterErrors();

            try {
                const response = await fetch('{{ route("staff.register-user") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-Token': document.querySelector('input[name="_token"]').value
                    },
                    body: JSON.stringify({
                        name: document.getElementById('reg_name').value,
                        surname: document.getElementById('reg_surname').value,
                        email: document.getElementById('reg_email').value,
                    })
                });

                let data;
                try {
                    data = await response.json();
                } catch (e) {
                    const msg = document.getElementById('register_message');
                    msg.textContent = 'Errore del server';
                    msg.className = 'text-center text-sm mt-2 text-red-600';
                    msg.classList.remove('hidden');
                    return;
                }

                if (!response.ok) {
                    if (data.errors) {
                        if (data.errors.name && data.errors.name.length > 0) {
                            document.getElementById('reg_name_error').textContent = data.errors.name.join(', ');
                            document.getElementById('reg_name_error').classList.remove('hidden');
                        }
                        if (data.errors.surname && data.errors.surname.length > 0) {
                            document.getElementById('reg_surname_error').textContent = data.errors.surname.join(', ');
                            document.getElementById('reg_surname_error').classList.remove('hidden');
                        }
                        if (data.errors.email && data.errors.email.length > 0) {
                            document.getElementById('reg_email_error').textContent = data.errors.email.join(', ');
                            document.getElementById('reg_email_error').classList.remove('hidden');
                        }
                    } else if (data.message) {
                        const msg = document.getElementById('register_message');
                        msg.textContent = data.message;
                        msg.className = 'text-center text-sm mt-2 text-red-600';
                        msg.classList.remove('hidden');
                    }
                } else {
                    // Utente registrato con successo
                    const newUser = data.user;
                    selectBuyer(newUser.id, newUser.name, newUser.surname, newUser.code, newUser.email, newUser.password);
                    closeRegisterModal();

                    const msg = document.getElementById('register_message');
                    msg.textContent = 'Acquirente registrato con successo!';
                    msg.className = 'text-center text-sm mt-2 text-green-600';
                    msg.classList.remove('hidden');
                    setTimeout(() => msg.classList.add('hidden'), 3000);
                }
            } catch (error) {

                const msg = document.getElementById('register_message');
                msg.textContent = 'Errore: ' + error.message;
                msg.className = 'text-center text-sm mt-2 text-red-600';
                msg.classList.remove('hidden');
            }
        });

        registerModal.addEventListener('click', (e) => {
            if (e.target === registerModal) {
                closeRegisterModal();
            }
        });

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
                    })
                    .catch(err => {});
            }, 300);
        });

        // Book Search with two filters
        let bookSearchDebounce;
        let bookTitleValue = '';
        let sellerCodeValue = '';

        function performBookSearch() {
            const title = bookTitleValue.trim();
            const sellerCode = sellerCodeValue.trim();

            // Show empty state if both fields are empty
            if (!title && !sellerCode) {
                document.getElementById('book_results_container').classList.add('hidden');
                document.getElementById('book_no_results').classList.add('hidden');
                document.getElementById('book_search_empty').classList.remove('hidden');
                return;
            }

            // Show loading state
            document.getElementById('book_search_empty').classList.add('hidden');
            document.getElementById('book_results_container').classList.add('hidden');
            document.getElementById('book_no_results').classList.add('hidden');

            const params = new URLSearchParams();
            if (title) params.append('title', title);
            if (sellerCode) params.append('seller_code', sellerCode);

            fetch(`{{ route('staff.sales.search-listings') }}?${params.toString()}`)
                .then(r => r.json())
                .then(data => {
                    const resultsTable = document.getElementById('book_results_table');
                    const resultsContainer = document.getElementById('book_results_container');
                    const noResults = document.getElementById('book_no_results');
                    const cartListingIds = cart.map(item => item.book_listing_id);
                    const availableBooks = data.filter(book => !cartListingIds.includes(book.id));

                    if (availableBooks.length === 0) {
                        resultsContainer.classList.add('hidden');
                        noResults.classList.remove('hidden');
                    } else {
                        // Condition color mapping
                        const conditionColors = {
                            'like-new': 'bg-green-100 text-green-800',
                            'good': 'bg-blue-100 text-blue-800',
                            'fair': 'bg-yellow-100 text-yellow-800',
                            'poor': 'bg-red-100 text-red-800'
                        };

                        const conditionLabels = {
                            'like-new': 'Come Nuovo',
                            'good': 'Buona',
                            'fair': 'Discreta',
                            'poor': 'Scarsa'
                        };

                        resultsTable.innerHTML = availableBooks.map(book => `
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 font-medium text-gray-900">${book.title}</td>
                                <td class="px-4 py-3 font-mono text-xs text-gray-600">${book.isbn || 'N/A'}</td>
                                <td class="px-4 py-3 text-gray-600">
                                    <div>${book.seller_name} ${book.seller_surname}</div>
                                    <div class="text-xs font-semibold text-gray-700">${book.seller_code}</div>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-1 rounded text-xs font-semibold ${conditionColors[book.condition] || 'bg-gray-100 text-gray-800'}">
                                        ${conditionLabels[book.condition] || book.condition}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right font-semibold text-gray-900">€${parseFloat(book.price).toFixed(2)}</td>
                                <td class="px-4 py-3 text-center">
                                    <button type="button" onclick="selectBook(${book.id}, '${book.title.replace(/'/g, "\\'")}', '${book.isbn || ''}', ${book.price}, '${book.condition}', '${book.seller_code}')" class="px-3 py-1 bg-green-600 text-white text-xs font-medium rounded hover:bg-green-700 transition">
                                        Seleziona
                                    </button>
                                </td>
                            </tr>
                        `).join('');
                        resultsContainer.classList.remove('hidden');
                        noResults.classList.add('hidden');
                    }
                })
                .catch(err => {
                    console.error('Errore nella ricerca:', err);
                    document.getElementById('book_no_results').classList.remove('hidden');
                });
        }

        document.getElementById('book_title_search').addEventListener('input', function(e) {
            bookTitleValue = e.target.value;
            clearTimeout(bookSearchDebounce);
            bookSearchDebounce = setTimeout(performBookSearch, 300);
        });

        document.getElementById('seller_code_search').addEventListener('input', function(e) {
            sellerCodeValue = e.target.value;
            clearTimeout(bookSearchDebounce);
            bookSearchDebounce = setTimeout(performBookSearch, 300);
        });

            // Load approved reservations from session if available
            const approvedReservations = {!! json_encode($approvedReservations ?? []) !!};
            const studentId = {{ $studentId ?? 'null' }};

            if (approvedReservations && approvedReservations.length > 0 && studentId) {
                // Auto-select the student as buyer FIRST
                fetch(`{{ route('staff.sales.search-buyers') }}?q=${studentId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.length > 0) {
                            const buyer = data[0];
                            selectBuyer(buyer.id, buyer.name, buyer.surname, buyer.code, buyer.email);
                            
                            // THEN add approved reservations with buyer_id
                            approvedReservations.forEach(reservation => {
                                cart.push({
                                    buyer_id: buyer.id,
                                    book_listing_id: reservation.book_listing_id,
                                    title: reservation.book_title,
                                    isbn: reservation.book_isbn || '',
                                    condition: reservation.book_condition || '',
                                    seller_code: reservation.seller_code || '',
                                    price: parseFloat(reservation.book_price),
                                });
                            });
                            
                            updateCartDisplay();
                            showToast(`✓ ${approvedReservations.length} libro/i caricato/i`, 'success');
                        }
                    })
                    .catch(err => {});
            }
        });
    </script>

    <!-- Modal Aggiunta Acquirente -->
    <div id="register_modal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-lg p-8 max-w-md w-full mx-4">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Aggiungi Acquirente</h2>
            <form id="register_form" class="space-y-4">
                @csrf
                <div>
                    <label for="reg_name" class="block text-sm font-semibold text-gray-900 mb-2">Nome <span class="text-red-600">*</span></label>
                    <input type="text" id="reg_name" name="name" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent" required>
                    <p id="reg_name_error" class="text-red-600 text-sm mt-1 hidden"></p>
                </div>
                <div>
                    <label for="reg_surname" class="block text-sm font-semibold text-gray-900 mb-2">Cognome <span class="text-red-600">*</span></label>
                    <input type="text" id="reg_surname" name="surname" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent" required>
                    <p id="reg_surname_error" class="text-red-600 text-sm mt-1 hidden"></p>
                </div>
                <div>
                    <label for="reg_email" class="block text-sm font-semibold text-gray-900 mb-2">Email <span class="text-red-600">*</span></label>
                    <input type="email" id="reg_email" name="email" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent" required>
                    <p id="reg_email_error" class="text-red-600 text-sm mt-1 hidden"></p>
                </div>
                <div class="flex space-x-3 pt-4">
                    <button type="submit" class="flex-1 px-4 py-2 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition">Aggiungi</button>
                    <button type="button" onclick="closeRegisterModal()" class="flex-1 px-4 py-2 text-gray-700 font-medium rounded-lg hover:bg-gray-100 transition">Annulla</button>
                </div>
                <p id="register_message" class="text-center text-sm mt-2 hidden"></p>
            </form>
        </div>
    </div>

    <!-- CSRF Token (hidden) -->
    @csrf
@endsection
