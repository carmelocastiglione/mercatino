@extends('layouts.app-staff')

@section('title', 'Acquisizioni')

@section('content')
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-4xl font-bold text-gray-900">Acquisizioni</h1>
            <p class="text-gray-600 mt-2">Gestione delle acquisizioni di libri dagli studenti</p>
        </div>
        <a href="{{ route('staff.acquisitions.create') }}" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium">
            + Nuova Acquisizione
        </a>
    </div>

    <!-- Error Messages -->
    @if ($errors->any())
        <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800">Errore</h3>
                    <div class="mt-2 text-sm text-red-700 whitespace-pre-wrap">
                        @foreach ($errors->all() as $error)
                            <p class="mb-1">{!! nl2br(e($error)) !!}</p>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if (session('error'))
        <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800">Errore</h3>
                    <p class="mt-2 text-sm text-red-700 whitespace-pre-wrap">{!! nl2br(e(session('error'))) !!}</p>
                </div>
            </div>
        </div>
    @endif

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <x-stats-card label="Acquisizioni Totali" :value="$totalAcquisitionsCount" color="indigo" />
        <x-stats-card label="Libri Acquisiti" :value="$totalBooksCount" color="indigo" />
        <x-stats-card label="Importo Totale" :value="$totalAcquisitionsAmount" color="indigo" formatted />
    </div>

    <!-- Filter Form -->
    <div class="bg-white border border-blue-200 rounded-lg p-6 mb-8">
        <form method="GET" action="{{ route('staff.acquisitions.index') }}" class="flex gap-2">
            <input 
                type="text" 
                name="q" 
                placeholder="Codice transazione, cognome, email o codice venditore..." 
                value="{{ $filterQuery }}"
                class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                autocomplete="off"
            />
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition">
                Filtra
            </button>
            @if($filterQuery)
                <a href="{{ route('staff.acquisitions.index') }}" class="px-6 py-2 bg-gray-500 text-white font-medium rounded-lg hover:bg-gray-600 transition">
                    Reset
                </a>
            @endif
        </form>
        @if($filterQuery)
            <p class="text-sm text-gray-600 mt-2">Risultati per: <strong>{{ $filterQuery }}</strong> ({{ $acquisitions->total() }} risultati)</p>
        @endif
    </div>

    @if($acquisitions->count() > 0)
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Codice Transazione</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Venditore</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Libri</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Totale</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Stato</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Data</th>
                            <th class="px-6 py-3 text-center text-sm font-semibold text-gray-900">Azioni</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($acquisitions as $acquisition)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 text-sm font-mono font-bold text-gray-900">
                                    {{ $acquisition->ean13 ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900 font-medium">
                                    <div>
                                        <p class="font-semibold">{{ $acquisition->seller->name }} {{ $acquisition->seller->surname }}</p>
                                        <p class="text-xs text-gray-500">{{ $acquisition->seller->code }}</p>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm font-semibold text-gray-900">
                                    {{ $acquisition->bookListings->count() }}
                                </td>
                                <td class="px-6 py-4 text-sm font-semibold text-gray-900">
                                    €{{ number_format($acquisition->total_price, 2) }}
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    <span class="px-3 py-1 rounded-full text-xs font-medium
                                        @if($acquisition->status === 'completed')
                                            bg-green-50 text-green-700
                                        @elseif($acquisition->status === 'pending')
                                            bg-yellow-50 text-yellow-700
                                        @else
                                            bg-red-50 text-red-700
                                        @endif
                                    ">
                                        @switch($acquisition->status)
                                            @case('completed')
                                                Completata
                                                @break
                                            @case('pending')
                                                In Sospeso
                                                @break
                                            @case('rejected')
                                                Rifiutata
                                                @break
                                            @default
                                                {{ ucfirst($acquisition->status) }}
                                        @endswitch
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    {{ $acquisition->created_at->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <a href="{{ route('staff.acquisitions.show', $acquisition->id) }}" class="px-4 py-2 bg-blue-600 text-white text-xs font-medium rounded-lg hover:bg-blue-700 transition inline-block">
                                        👁️ Visualizza
                                    </a>
                                    <button onclick="confirmDeleteAcquisition({{ $acquisition->id }}, '{{ $acquisition->ean13 }}', {{ $acquisition->bookListings->count() }}, '{{ number_format($acquisition->total_price, 2) }}')" class="ml-2 px-4 py-2 bg-red-600 text-white text-xs font-medium rounded-lg hover:bg-red-700 transition inline-block">
                                        🗑️ Elimina
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4 border-t border-gray-200">
                {{ $acquisitions->links() }}
            </div>
        </div>
    @else
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-12 text-center">
            <div class="text-5xl mb-4">📚</div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Nessuna acquisizione</h3>
            <p class="text-gray-600 mb-6">Non hai ancora acquisito libri. Inizia ad aggiungerne!</p>
            <a href="{{ route('staff.acquisitions.create') }}" class="inline-block px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium">
                + Nuova Acquisizione
            </a>
        </div>
    @endif

    <!-- Hidden form for acquisition deletion -->
    <form id="deleteAcquisitionForm" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>

    <script>
        function confirmDeleteAcquisition(acquisitionId, ean13, booksCount, totalPrice) {
            const message = `⚠️ ATTENZIONE!\n\nStai per eliminare l'INTERA ACQUISIZIONE:\n\n` +
                `Codice: ${ean13}\n` +
                `Libri: ${booksCount}\n` +
                `Importo: €${totalPrice}\n\n` +
                `Questa azione:\n` +
                `✓ Eliminerà TUTTI i ${booksCount} libro/i dell'acquisizione\n` +
                `✓ I resi verranno eliminati automaticamente\n` +
                `✓ Se ci sono vendite, prenotazioni, riscossioni o ritiri, l'operazione sarà bloccata\n` +
                `✓ NON può essere annullata\n\n` +
                `Sei sicuro di voler procedere?`;

            if (confirm(message)) {
                const form = document.getElementById('deleteAcquisitionForm');
                form.action = `/staff/acquisitions/${acquisitionId}`;
                form.submit();
            }
        }
    </script>
@endsection
