@extends('layouts.app-staff')

@section('content')
<div class="max-w-2xl mx-auto py-12 sm:px-6 lg:px-8">
    <div class="px-4 sm:px-0">
        <!-- Header -->
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold text-gray-900 mb-2">Gestione Riscossioni</h1>
            <p class="text-gray-600">Seleziona un venditore per gestire i suoi ritiri</p>
        </div>

        <!-- Search Bar -->
        <div class="relative">
            <div class="bg-white shadow-lg rounded-lg overflow-hidden">
                <input 
                    type="text" 
                    id="seller-search" 
                    placeholder="Digita nome, cognome, codice o email..." 
                    class="w-full px-6 py-4 text-lg border-0 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                    autocomplete="off">
                
                <!-- Results Dropdown -->
                <div id="search-results" class="hidden absolute top-full left-0 right-0 mt-2 bg-white border border-gray-300 rounded-lg shadow-lg z-10 max-h-96 overflow-y-auto">
                    <!-- Results will be populated by JavaScript -->
                </div>
            </div>
        </div>

        <!-- No Results Message -->
        <div id="no-results" class="hidden text-center py-12">
            <div class="text-gray-500 text-lg">Inizia a digitare per cercare un venditore...</div>
        </div>
    </div>
</div>

<script>
let debounceTimer;

document.getElementById('seller-search').addEventListener('input', function(e) {
    clearTimeout(debounceTimer);
    const query = e.target.value.trim();

    // Show no results message if empty
    if (query.length === 0) {
        document.getElementById('search-results').classList.add('hidden');
        document.getElementById('no-results').classList.remove('hidden');
        return;
    }

    // Hide no results if user starts typing
    document.getElementById('no-results').classList.add('hidden');

    // Only search if at least 2 characters
    if (query.length < 2) {
        document.getElementById('search-results').classList.add('hidden');
        return;
    }

    debounceTimer = setTimeout(() => {
        fetch(`{{ route('staff.withdrawals.search-sellers') }}?q=${encodeURIComponent(query)}`)
            .then(response => {
                if (!response.ok) throw new Error('Network response was not ok');
                return response.json();
            })
            .then(data => {
                const resultsDiv = document.getElementById('search-results');
                resultsDiv.innerHTML = '';

                if (data.length === 0) {
                    resultsDiv.innerHTML = '<div class="px-6 py-8 text-center text-gray-500">Nessun venditore trovato</div>';
                    resultsDiv.classList.remove('hidden');
                    return;
                }

                data.forEach((seller, index) => {
                    const div = document.createElement('a');
                    div.href = `/staff/withdrawals/${seller.id}/process`;
                    div.className = 'block px-6 py-4 hover:bg-indigo-50 border-b border-gray-100 last:border-b-0 transition-colors';
                    div.innerHTML = `
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="font-semibold text-gray-900">${seller.name} ${seller.surname}</div>
                                <div class="text-sm text-gray-600">${seller.email} • ${seller.code}</div>
                            </div>
                            <div class="text-right">
                                <div class="text-indigo-600 font-semibold">→</div>
                            </div>
                        </div>
                    `;
                    resultsDiv.appendChild(div);
                });

                resultsDiv.classList.remove('hidden');
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('search-results').innerHTML = '<div class="px-6 py-4 text-center text-red-500">Errore nella ricerca</div>';
                document.getElementById('search-results').classList.remove('hidden');
            });
    }, 300);
});

// Hide results when clicking outside
document.addEventListener('click', function(e) {
    if (!e.target.closest('#seller-search') && !e.target.closest('#search-results')) {
        document.getElementById('search-results').classList.add('hidden');
    }
});
</script>
@endsection
