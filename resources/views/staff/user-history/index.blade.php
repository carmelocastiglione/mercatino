@extends('layouts.app-staff')

@section('title', 'Storico Utente')

@section('content')
    <div class="mb-8">
        <h1 class="text-4xl font-bold text-gray-900">Storico Utente</h1>
        <p class="text-gray-600 mt-2">Cerca un utente per visualizzare tutti i suoi movimenti</p>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-8 max-w-2xl mx-auto">
        <label for="user_search" class="block text-sm font-semibold text-gray-900 mb-2">
            Ricerca Utente
        </label>
        <div class="relative">
            <input 
                type="text" 
                id="user_search" 
                placeholder="Cerca per nome, cognome, email o codice..."
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-lg"
                autocomplete="off"
            >
            <div id="user_results" class="absolute top-full left-0 right-0 mt-1 bg-white border border-gray-300 rounded-lg shadow-lg hidden max-h-72 overflow-y-auto z-10"></div>
        </div>
    </div>

    <script>
        const userSearchInput = document.getElementById('user_search');
        const userResults = document.getElementById('user_results');

        userSearchInput.addEventListener('input', async function() {
            const query = this.value.trim();
            
            if (query.length < 2) {
                userResults.classList.add('hidden');
                return;
            }

            try {
                const response = await fetch(`{{ route('staff.user-history.search') }}?q=${encodeURIComponent(query)}`);
                const users = await response.json();

                if (users.length === 0) {
                    userResults.innerHTML = '<div class="p-4 text-gray-500 text-center">Nessun utente trovato</div>';
                    userResults.classList.remove('hidden');
                    return;
                }

                userResults.innerHTML = users.map(user => `
                    <button type="button" onclick="selectUser(${user.id})" class="w-full text-left px-4 py-3 hover:bg-blue-50 transition border-b border-gray-200 last:border-b-0">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="font-medium text-gray-900">${user.name} ${user.surname}</p>
                                <p class="text-sm text-gray-500">${user.code}</p>
                            </div>
                            <p class="text-xs text-gray-400">${user.email}</p>
                        </div>
                    </button>
                `).join('');

                userResults.classList.remove('hidden');
            } catch (error) {
                console.error('Errore nella ricerca:', error);
                userResults.innerHTML = '<div class="p-4 text-red-500">Errore nella ricerca</div>';
                userResults.classList.remove('hidden');
            }
        });

        function selectUser(userId) {
            window.location.href = `{{ route('staff.user-history.show', ':id') }}`.replace(':id', userId);
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            if (!event.target.closest('#user_search') && !event.target.closest('#user_results')) {
                userResults.classList.add('hidden');
            }
        });
    </script>
@endsection
