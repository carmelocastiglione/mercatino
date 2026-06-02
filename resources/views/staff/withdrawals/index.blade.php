@extends('layouts.app-staff')

@section('title', 'Riscossioni')

@section('content')
    <div class="mb-8">
        <div class="mb-8">
            <h1 class="text-4xl font-bold text-gray-900">Riscossioni</h1>
            <p class="text-gray-600 mt-2">Gestione delle riscossioni degli studenti</p>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <x-stats-card label="Totale Ricavi Vendite" :value="$totalEarned" color="green" formatted />
            <x-stats-card label="Totale Da Riscuotere" :value="$totalAvailable" color="blue" formatted />
            <x-stats-card label="Totale Riscosso" :value="$totalWithdrawn" color="red" formatted />
        </div>

        <!-- Progress Bar -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-8">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-lg font-semibold text-gray-900">Progresso Riscossioni</h3>
                <span class="text-sm font-medium text-gray-600">{{ $usersWithdrawn }} / {{ $usersWithSoldBooks }} utenti</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-3">
                <div class="bg-gradient-to-r from-green-500 to-green-600 h-3 rounded-full transition-all duration-300" style="width: {{ $withdrawalProgress }}%"></div>
            </div>
            <p class="text-sm text-gray-600 mt-3">{{ round($withdrawalProgress) }}% degli utenti con libri venduti ha ritirato i propri guadagni</p>
        </div>

        <!-- SECTION: SEARCH SELLERS FOR WITHDRAWAL -->
        <div class="bg-gradient-to-r from-indigo-50 to-blue-50 border border-indigo-200 rounded-lg p-6 mb-8">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">🔍 Ricerca Venditore</h2>
            <p class="text-sm text-gray-600 mb-4">Cerca un venditore per gestire rapidamente le sue riscossioni e ritiri.</p>
            
            <div class="relative">
                <input 
                    type="text" 
                    id="seller-search" 
                    placeholder="Digita nome, cognome, codice o email..." 
                    class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                    autocomplete="off">
                
                <!-- Results Dropdown -->
                <div id="search-results" class="hidden absolute top-full left-0 right-0 mt-2 bg-white border border-gray-300 rounded-lg shadow-lg z-10 max-h-96 overflow-y-auto">
                    <!-- Results will be populated by JavaScript -->
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                {{ session('success') }}
            </div>
        @endif

        <!-- Sellers Table -->
        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <table class="w-full">
                <thead class="bg-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Venditore</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Codice</th>
                        <th class="px-6 py-3 text-right text-sm font-semibold text-gray-900">Totale Vendite</th>
                        <th class="px-6 py-3 text-right text-sm font-semibold text-gray-900">Già Riscosso</th>
                        <th class="px-6 py-3 text-right text-sm font-semibold text-gray-900">Da Riscuotere</th>
                        <th class="px-6 py-3 text-center text-sm font-semibold text-gray-900">Azioni</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($sellers as $seller)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm text-gray-900">
                                <strong>{{ $seller->name }} {{ $seller->surname }}</strong>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ $seller->code }}
                            </td>
                            <td class="px-6 py-4 text-right text-sm text-gray-900">
                                <strong class="text-green-600">{{ number_format($seller->getTotalSalesAmount(), 2, ',', '.') }}€</strong>
                            </td>
                            <td class="px-6 py-4 text-right text-sm text-gray-900">
                                <strong class="text-red-600">{{ number_format($seller->getTotalWithdrawnAmount(), 2, ',', '.') }}€</strong>
                            </td>
                            <td class="px-6 py-4 text-right text-sm">
                                @php
                                    $balance = $seller->getAvailableBalance();
                                @endphp
                                <strong class="@if($balance > 0) text-blue-600 @else text-gray-500 @endif">
                                    {{ number_format($balance, 2, ',', '.') }}€
                                </strong>
                            </td>
                            <td class="px-6 py-4 text-center text-sm">
                                <a href="{{ route('staff.withdrawals.process-seller', $seller->id) }}" class="text-indigo-600 hover:text-indigo-900 font-semibold">
                                    Visualizza
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                Nessun venditore con vendite trovato
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-6">
            {{ $sellers->links() }}
        </div>
    </div>
</div>

<script>
let debounceTimer;

document.getElementById('seller-search').addEventListener('input', function(e) {
    clearTimeout(debounceTimer);
    const query = e.target.value.trim();

    if (query.length === 0) {
        document.getElementById('search-results').classList.add('hidden');
        return;
    }

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
