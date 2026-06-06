@extends('layouts.app-staff')

@section('title', 'Storico Utente')

@section('content')
    <!-- Search Box (commented out for now, to be reintroduced in future updates)
    <div class="mb-8">
        <h1 class="text-4xl font-bold text-gray-900">Storico Utente</h1>
        <p class="text-gray-600 mt-2">Cerca un utente per visualizzare tutti i suoi movimenti</p>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-8 max-w-2xl mx-auto mb-8">
        <label for="user_search" class="block text-sm font-semibold text-gray-900 mb-2">
            Ricerca Utente
        </label>
        <div class="relative">
            <input 
                type="text" 
                id="user_search" 
                placeholder="Cerca per cognome, email o codice..."
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-lg"
                autocomplete="off"
                @if($user) value="{{ $user->name }} {{ $user->surname }}" @endif
            >
            <div id="user_results" class="absolute top-full left-0 right-0 mt-1 bg-white border border-gray-300 rounded-lg shadow-lg hidden max-h-72 overflow-y-auto z-10"></div>
        </div>
    </div>
    -->
   
        <div class="mb-8">
            <a href="{{ route('staff.users.index') }}" class="text-blue-600 hover:text-blue-800 font-medium">← Torna a Utenti</a>
        </div>

        <div class="mb-8 flex items-center justify-between">
            <div>
                <h1 class="text-4xl font-bold text-gray-900">Storico di {{ $user->name }} {{ $user->surname }}</h1>
                <div class="flex items-center gap-4 mt-3">
                    <p class="text-sm text-gray-600"><strong>Codice:</strong> {{ $user->code }}</p>
                    <p class="text-sm text-gray-600"><strong>Email:</strong> {{ $user->email }}</p>
                </div>
            </div>
        </div>

        @if($user && count($movements) > 0)
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <!-- Table Header -->
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900 w-12"></th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900">Tipo</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900">Descrizione</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900">Data</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach ($movements as $index => $movement)
                            <tr class="hover:bg-gray-50 transition cursor-pointer" onclick="toggleRow({{ $index }})">
                                <td class="px-6 py-4 text-center">
                                    <span class="toggle-icon" data-index="{{ $index }}">
                                        <svg class="w-5 h-5 text-gray-600 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm font-medium text-gray-900">
                                    <span class="mr-2">{{ $movement['icon'] }}</span>{{ $movement['title'] }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $movement['description'] }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">{{ $movement['date']->format('d/m/Y H:i') }}</td>
                            </tr>

                            <!-- Details Row (Collapsible) -->
                            <tr class="details-row hidden" id="details-{{ $index }}">
                                <td colspan="4" class="px-6 py-6 bg-gray-50">
                                    <div class="space-y-4">
                                        @if ($movement['type'] === 'reservation_batch')
                                            <div class="space-y-4">
                                                <div>
                                                    <div class="flex items-center justify-between mb-3">
                                                        <p class="text-xs text-gray-500 font-semibold uppercase">Libri Prenotati</p>
                                                    </div>
                                                    <div class="space-y-2">
                                                        @foreach ($movement['data']->bookReservations as $reservation)
                                                            <div class="flex items-center justify-between gap-4">
                                                                <p class="text-sm font-medium text-gray-900">{{ $reservation->bookListing->book->title ?? 'N/A' }}</p>
                                                                <p class="text-xs text-gray-500">ISBN: {{ $reservation->bookListing->book->isbn ?? 'N/A' }}</p>
                                                                <p class="text-sm font-medium text-gray-900 whitespace-nowrap">€{{ number_format($reservation->bookListing->price, 2) }}</p>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        @elseif ($movement['type'] === 'delivery_batch')
                                            <div class="space-y-4">
                                                <div>
                                                    <div class="flex items-center justify-between mb-3">
                                                        <p class="text-xs text-gray-500 font-semibold uppercase">Libri Consegnati</p>
                                                    </div>
                                                    <div class="space-y-2">
                                                        @foreach ($movement['data']->deliveries as $delivery)
                                                            <div class="flex items-center justify-between gap-4">
                                                                <p class="text-sm font-medium text-gray-900">{{ $delivery->book->title ?? 'N/A' }}</p>
                                                                <p class="text-xs text-gray-500">ISBN: {{ $delivery->book->isbn ?? 'N/A' }}</p>
                                                                <p class="text-sm font-medium text-gray-900 whitespace-nowrap">€{{ number_format($delivery->price, 2) }}</p>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        @elseif ($movement['type'] === 'acquisition')
                                            <div class="space-y-4">
                                                <div>
                                                    <div class="flex items-center justify-between mb-3">
                                                        <p class="text-xs text-gray-500 font-semibold uppercase">Libri Acquisiti</p>
                                                        <a href="{{ route('staff.acquisitions.show', $movement['data']) }}" class="text-blue-600 hover:text-blue-800 text-xs font-semibold uppercase">Visualizza Ricevuta</a>
                                                    
                                                    </div>
                                                    <div class="space-y-2">
                                                        @foreach ($movement['data']->bookListings as $bookListing)
                                                            <div class="flex items-center justify-between gap-4">
                                                                <p class="text-sm font-medium text-gray-900">{{ $bookListing->book->title ?? 'N/A' }}</p>
                                                                <p class="text-xs text-gray-500">ISBN: {{ $bookListing->book->isbn ?? 'N/A' }}</p>
                                                                <p class="text-sm font-medium text-gray-900 whitespace-nowrap">€{{ number_format($bookListing->price, 2) }}</p>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        @elseif ($movement['type'] === 'purchase_batch')
                                            <div class="space-y-4">
                                                <div>
                                                    <div class="flex items-center justify-between mb-3">
                                                        <p class="text-xs text-gray-500 font-semibold uppercase">Libri Acquistati</p>
                                                        <a href="{{ route('staff.sales.show', $movement['data']) }}" class="text-blue-600 hover:text-blue-800 text-xs font-semibold uppercase">Visualizza Ricevuta</a>
                                                    </div>
                                                    <div class="space-y-2">
                                                        @foreach ($movement['data']->sales as $sale)
                                                            <div class="flex items-center justify-between gap-4">
                                                                <p class="text-sm font-medium text-gray-900">{{ $sale->bookListing->book->title ?? 'N/A' }}</p>
                                                                <p class="text-xs text-gray-500">ISBN: {{ $sale->bookListing->book->isbn ?? 'N/A' }}</p>
                                                                <p class="text-sm font-medium text-gray-900 whitespace-nowrap">€{{ number_format($sale->bookListing->price_sell, 2) }}</p>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        @elseif ($movement['type'] === 'sale')
                                            <div class="space-y-4">
                                                <div>
                                                    <div class="flex items-center justify-between mb-3">
                                                        <p class="text-xs text-gray-500 font-semibold uppercase">Libro Venduto</p>
                                                        <a href="{{ route('staff.sales.show', $movement['data']->batch) }}" class="text-blue-600 hover:text-blue-800 text-xs font-semibold uppercase">Visualizza Ricevuta</a>
                                                    </div>
                                                    <div class="space-y-2">
                                                        <div class="flex items-center justify-between gap-4">
                                                            <p class="text-sm font-medium text-gray-900">{{ $movement['data']->bookListing->book->title ?? 'N/A' }}</p>
                                                            <p class="text-xs text-gray-500">ISBN: {{ $movement['data']->bookListing->book->isbn ?? 'N/A' }}</p>
                                                            <p class="text-sm font-medium text-gray-900 whitespace-nowrap">€{{ number_format($movement['data']->bookListing->price_sell, 2) }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @elseif ($movement['type'] === 'withdrawal_batch')
                                            <div class="space-y-4">
                                                <div>
                                                    <div class="flex items-center justify-between mb-3">
                                                        <p class="text-xs text-gray-500 font-semibold uppercase">Libri Ritirati</p>
                                                        <a href="{{ route('staff.withdrawals.show-batch', $movement['data']) }}" class="text-blue-600 hover:text-blue-800 text-xs font-semibold uppercase">Visualizza Ricevuta</a>
                                                    </div>
                                                    <div class="space-y-2">
                                                        @foreach ($movement['data']->withdrawals as $withdrawal)
                                                            <div class="flex items-center justify-between gap-4">
                                                                <p class="text-sm font-medium text-gray-900">{{ $withdrawal->bookListing->book->title ?? 'N/A' }}</p>
                                                                <p class="text-xs text-gray-500">ISBN: {{ $withdrawal->bookListing->book->isbn ?? 'N/A' }}</p>
                                                                <p class="text-sm font-medium text-gray-900 whitespace-nowrap">€{{ number_format($withdrawal->amount, 2) }}</p>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        @elseif ($movement['type'] === 'pickup_batch')
                                            <div class="space-y-4">
                                                <div>
                                                    <div class="flex items-center justify-between mb-3">
                                                        <p class="text-xs text-gray-500 font-semibold uppercase">Libri Ritirati</p>
                                                        <a href="{{ route('staff.withdrawals.pickup-summary', $movement['data']) }}" class="text-blue-600 hover:text-blue-800 text-xs font-semibold uppercase">Visualizza Ricevuta</a>
                                                    </div>
                                                    <div class="space-y-2">
                                                        @foreach ($movement['data']->pickups as $pickup)
                                                            <div class="flex items-center justify-between gap-4">
                                                                <p class="text-sm font-medium text-gray-900">{{ $pickup->bookListing->book->title ?? 'N/A' }}</p>
                                                                <p class="text-xs text-gray-500">ISBN: {{ $pickup->bookListing->book->isbn ?? 'N/A' }}</p>
                                                                <span class="px-2 py-1 rounded text-xs font-semibold whitespace-nowrap
                                                                    @if ($pickup->leave)
                                                                        bg-green-100 text-green-800
                                                                    @else
                                                                        bg-blue-100 text-blue-800
                                                                    @endif
                                                                ">
                                                                    @if ($pickup->leave)
                                                                        Archiviato
                                                                    @else
                                                                        Ritirato
                                                                    @endif
                                                                </span>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        @elseif ($movement['type'] === 'reclaim_by_buyer')
                                            <div class="space-y-4">
                                                <div>
                                                    <div class="flex items-center justify-between mb-3">
                                                        <p class="text-xs text-gray-500 font-semibold uppercase">Libro Reso</p>
                                                        <a href="{{ route('staff.reclaims.show', $movement['data']) }}" class="text-blue-600 hover:text-blue-800 text-xs font-semibold uppercase">Visualizza Ricevuta</a>
                                                    </div>
                                                    <div class="space-y-2">
                                                        <div class="flex items-center justify-between gap-4">
                                                            <p class="text-sm font-medium text-gray-900">{{ $movement['data']->bookListing->book->title ?? 'N/A' }}</p>
                                                            <p class="text-xs text-gray-500">ISBN: {{ $movement['data']->bookListing->book->isbn ?? 'N/A' }}</p>
                                                            <span class="px-2 py-1 rounded text-xs font-semibold whitespace-nowrap
                                                                @if ($movement['data']->status === 'approved')
                                                                    bg-green-100 text-green-800
                                                                @elseif ($movement['data']->status === 'rejected')
                                                                    bg-red-100 text-red-800
                                                                @else
                                                                    bg-yellow-100 text-yellow-800
                                                                @endif
                                                            ">
                                                                @if ($movement['data']->status === 'approved')
                                                                    Approvato
                                                                @elseif ($movement['data']->status === 'rejected')
                                                                    Rifiutato
                                                                @else
                                                                    In Sospeso
                                                                @endif
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @elseif ($movement['type'] === 'reclaim_by_seller')
                                            <div class="space-y-4">
                                                <div>
                                                    <div class="flex items-center justify-between mb-3">
                                                        <p class="text-xs text-gray-500 font-semibold uppercase">Libro Reso dell'Utente</p>
                                                        <a href="{{ route('staff.reclaims.show', $movement['data']) }}" class="text-blue-600 hover:text-blue-800 text-xs font-semibold uppercase">Visualizza Ricevuta</a>
                                                    </div>
                                                    <div class="space-y-2">
                                                        <div class="flex items-center justify-between gap-4">
                                                            <p class="text-sm font-medium text-gray-900">{{ $movement['data']->bookListing->book->title ?? 'N/A' }}</p>
                                                            <p class="text-xs text-gray-500">ISBN: {{ $movement['data']->bookListing->book->isbn ?? 'N/A' }}</p>
                                                            <span class="px-2 py-1 rounded text-xs font-semibold whitespace-nowrap
                                                                @if ($movement['data']->status === 'approved')
                                                                    bg-green-100 text-green-800
                                                                @elseif ($movement['data']->status === 'rejected')
                                                                    bg-red-100 text-red-800
                                                                @else
                                                                    bg-yellow-100 text-yellow-800
                                                                @endif
                                                            ">
                                                                @if ($movement['data']->status === 'approved')
                                                                    Approvato
                                                                @elseif ($movement['data']->status === 'rejected')
                                                                    Rifiutato
                                                                @else
                                                                    In Sospeso
                                                                @endif
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @elseif($user && count($movements) === 0)
        <div class="bg-gray-50 border-2 border-gray-300 rounded-lg p-12 text-center">
            <p class="text-gray-600 font-medium">Nessun movimento trovato</p>
            <p class="text-sm text-gray-500 mt-1">Questo utente non ha ancora effettuato operazioni</p>
        </div>
    @endif

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
            window.location.href = `{{ route('staff.user-history.index') }}`.replace(/\/$/, '') + `/${userId}`;
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            if (!event.target.closest('#user_search') && !event.target.closest('#user_results')) {
                userResults.classList.add('hidden');
            }
        });

        // Toggle Row Function
        function toggleRow(index) {
            const detailsRow = document.getElementById('details-' + index);
            const icon = document.querySelector('[data-index="' + index + '"]');
            
            // Toggle display
            detailsRow.classList.toggle('hidden');
            
            // Rotate icon
            icon.querySelector('svg').classList.toggle('rotate-90');
        }
    </script>
@endsection
