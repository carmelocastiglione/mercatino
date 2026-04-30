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
        <!-- Seller Code Box -->
        <div id="seller_code_box" class="mb-6 hidden bg-gradient-to-r from-blue-50 to-blue-100 border-2 border-blue-300 rounded-lg p-6 shadow-md">
            <p class="text-sm text-center text-gray-600 mb-2">Codice venditore</p>
            <p id="seller_code_display" class="text-5xl font-bold text-blue-600 text-center tracking-widest"></p>
        </div>

        <!-- Seller Password Box -->
        <div id="seller_password_box" class="mb-6 hidden bg-gradient-to-r from-green-50 to-green-100 border-2 border-green-300 rounded-lg p-6 shadow-md">
            <p class="text-sm text-center text-gray-600 mb-4">Credenziali di accesso</p>
            <div class="space-y-4">
                <div>
                    <p class="text-xs text-center text-gray-600 mb-2">Email:</p>
                    <p id="seller_email_display" class="font-mono bg-white px-4 py-3 rounded border border-green-200 text-green-700 text-center text-lg break-all"></p>
                </div>
                <div>
                    <p class="text-xs text-center text-gray-600 mb-2">Password:</p>
                    <code id="seller_password_display" class="font-mono bg-white px-4 py-3 rounded border border-green-200 text-green-700 block text-center text-lg"></code>
                </div>
            </div>
        </div>

        <form action="{{ route('staff.listings.store') }}" method="POST" class="bg-white rounded-lg shadow-sm border border-gray-200 p-8">
            @csrf

            <!-- Seller Selection -->
            <div class="mb-8">
                <label for="seller_search" class="block text-sm font-semibold text-gray-900 mb-2">
                    Venditore <span class="text-red-600">*</span>
                </label>
                <div class="flex gap-2">
                    <div class="relative flex-1">
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
                    <button type="button" onclick="openRegisterModal()" class="px-4 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition whitespace-nowrap">
                        + Registra
                    </button>
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

    <!-- Modal Registrazione Venditore -->
    <div id="register_modal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-lg p-8 max-w-md w-full mx-4">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Registra Venditore</h2>
            <form id="register_form" class="space-y-4">
                @csrf
                <div>
                    <label for="reg_name" class="block text-sm font-semibold text-gray-900 mb-2">Nome <span class="text-red-600">*</span></label>
                    <input type="text" id="reg_name" name="name" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                    <p id="reg_name_error" class="text-red-600 text-sm mt-1 hidden"></p>
                </div>
                <div>
                    <label for="reg_surname" class="block text-sm font-semibold text-gray-900 mb-2">Cognome <span class="text-red-600">*</span></label>
                    <input type="text" id="reg_surname" name="surname" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                    <p id="reg_surname_error" class="text-red-600 text-sm mt-1 hidden"></p>
                </div>
                <div>
                    <label for="reg_email" class="block text-sm font-semibold text-gray-900 mb-2">Email <span class="text-red-600">*</span></label>
                    <input type="email" id="reg_email" name="email" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                    <p id="reg_email_error" class="text-red-600 text-sm mt-1 hidden"></p>
                </div>
                <div class="flex space-x-3 pt-4">
                    <button type="submit" class="flex-1 px-4 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition">Registra</button>
                    <button type="button" onclick="closeRegisterModal()" class="flex-1 px-4 py-2 text-gray-700 font-medium rounded-lg hover:bg-gray-100 transition">Annulla</button>
                </div>
                <p id="register_message" class="text-center text-sm mt-2 hidden"></p>
            </form>
        </div>
    </div>

    <script>
        // === SELLER SEARCH ===
        const sellerSearch = document.getElementById('seller_search');
        const sellerResults = document.getElementById('seller_results');
        const sellerIdInput = document.getElementById('seller_id');
        const selectedSellerDiv = document.getElementById('selected_seller');
        const selectedSellerText = document.getElementById('selected_seller_text');
        const sellerCodeBox = document.getElementById('seller_code_box');
        const sellerCodeDisplay = document.getElementById('seller_code_display');
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
                            <div class="p-4 border-b border-gray-200 hover:bg-gray-50 cursor-pointer transition" onclick="selectSeller(${user.id}, '${user.name.replace(/'/g, "\\'")} ${user.surname.replace(/'/g, "\\'")}', '${user.code}')">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <p class="font-medium text-gray-900">${user.name} ${user.surname}</p>
                                        <p class="text-sm text-gray-500">${user.email}</p>
                                    </div>
                                    <p class="text-sm font-semibold text-blue-600 bg-blue-50 px-3 py-1 rounded">${user.code}</p>
                                </div>
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

        function selectSeller(id, name, code, email, password) {
            sellerIdInput.value = id;
            sellerSearch.value = name;
            sellerResults.classList.add('hidden');
            selectedSellerText.textContent = name;
            selectedSellerDiv.classList.remove('hidden');
            sellerCodeDisplay.textContent = code;
            sellerCodeBox.classList.remove('hidden');
            
            // Nascondi le credenziali di default
            document.getElementById('seller_password_box').classList.add('hidden');
            
            if (password) {
                document.getElementById('seller_email_display').textContent = email;
                document.getElementById('seller_password_display').textContent = password;
                document.getElementById('seller_password_box').classList.remove('hidden');
            }
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

        // === MODAL REGISTRAZIONE ===
        const registerModal = document.getElementById('register_modal');
        const registerForm = document.getElementById('register_form');

        function openRegisterModal() {
            registerModal.classList.remove('hidden');
            document.getElementById('reg_name').focus();
        }

        function closeRegisterModal() {
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
                    const text = await response.text();
                    console.error('Risposta non JSON:', text);
                    const msg = document.getElementById('register_message');
                    msg.textContent = 'Errore del server. Controlla la console.';
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
                    return;
                }

                // Utente registrato con successo
                const newUser = data.user;
                selectSeller(newUser.id, `${newUser.name} ${newUser.surname}`, newUser.code, newUser.email, newUser.password);
                closeRegisterModal();

                const msg = document.getElementById('register_message');
                msg.textContent = 'Venditore registrato con successo!';
                msg.className = 'text-center text-sm mt-2 text-green-600';
                msg.classList.remove('hidden');
                setTimeout(() => msg.classList.add('hidden'), 3000);

            } catch (error) {
                console.error('Errore:', error);
                const msg = document.getElementById('register_message');
                msg.textContent = 'Errore durante la registrazione: ' + error.message;
                msg.className = 'text-center text-sm mt-2 text-red-600';
                msg.classList.remove('hidden');
            }
        });

        // Chiudi modale quando clicca fuori
        registerModal.addEventListener('click', (e) => {
            if (e.target === registerModal) {
                closeRegisterModal();
            }
        });
    </script>
@endsection
