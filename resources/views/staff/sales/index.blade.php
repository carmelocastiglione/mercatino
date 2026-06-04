@extends('layouts.app-staff')

@section('title', 'Vendite')

@section('content')
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-4xl font-bold text-gray-900">Vendite</h1>
            <p class="text-gray-600 mt-2">Gestione delle vendite al mercatino</p>
        </div>
        <a href="{{ route('staff.sales.create') }}" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-medium">
            + Nuova Vendita
        </a>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <x-stats-card label="Vendite Totali" :value="$totalBatchesCount" color="green" />
        <x-stats-card label="Incasso Totale" :value="$totalRevenue" color="green" formatted />
    </div>

    @if(session('success'))
        <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    <!-- Info Box: Multiple Books Selection -->
    <x-info-box
        type="info"
        title="Avviso conteggio vendite"
        message="Questa pagina registra le vendite senza tener conto dei resi. Se un libro è stato venduto e poi restituito, la vendita originale rimarrà registrata qui. Le vendite totali e l'incasso totale sono conteggiati senza tener conto delle vendite. Per una gestione accurata dei resi, consulta la sezione 'Resi' dedicata. Per controllare l'incasso effettivo comprensivo di resi, consulta la sezione 'Riscossioni'."
    />

    <!-- Filter Form -->
    <div class="bg-white border border-green-200 rounded-lg p-6 mb-8">
        <form method="GET" action="{{ route('staff.sales.index') }}" class="flex gap-2">
            <input 
                type="text" 
                name="q" 
                placeholder="Filtra per nome, cognome, email o codice acquirente..." 
                value="{{ $filterQuery }}"
                class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                autocomplete="off"
            />
            <button type="submit" class="px-6 py-2 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition">
                Filtra
            </button>
            @if($filterQuery)
                <a href="{{ route('staff.sales.index') }}" class="px-6 py-2 bg-gray-500 text-white font-medium rounded-lg hover:bg-gray-600 transition">
                    Reset
                </a>
            @endif
        </form>
        @if($filterQuery)
            <p class="text-sm text-gray-600 mt-2">Risultati per: <strong>{{ $filterQuery }}</strong> ({{ $batches->total() }} risultati)</p>
        @endif
    </div>

    @if($batches->count() > 0)
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Acquirente</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Staff</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Libri Venduti</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Totale</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Data</th>
                            <th class="px-6 py-3 text-center text-sm font-semibold text-gray-900">Azioni</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($batches as $batch)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    @if($batch->buyer)
                                        <p class="font-medium">{{ $batch->buyer->name }} {{ $batch->buyer->surname }}</p>
                                        <p class="text-xs text-gray-500">{{ $batch->buyer->code }}</p>
                                    @else
                                        <span class="text-gray-500">N/A</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900 font-medium">
                                    {{ $batch->creator->name }} {{ $batch->creator->surname }}
                                </td>
                                <td class="px-6 py-4 text-sm font-semibold text-gray-900">
                                    {{ $batch->sales->count() }}
                                </td>
                                <td class="px-6 py-4 text-sm font-semibold text-gray-900">
                                    €{{ number_format($batch->total_price, 2) }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    {{ $batch->created_at->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <a href="{{ route('staff.sales.show', $batch->id) }}" class="px-4 py-2 bg-green-600 text-white text-xs font-medium rounded-lg hover:bg-green-700 transition inline-block">
                                        👁️ Visualizza
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4 border-t border-gray-200">
                {{ $batches->links() }}
            </div>
        </div>
    @else
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-12 text-center">
            <div class="text-5xl mb-4">🛍️</div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Nessuna vendita</h3>
            <p class="text-gray-600 mb-6">Nessuna vendita è stata ancora creata. Inizia a registrare le vendite!</p>
            <a href="{{ route('staff.sales.create') }}" class="inline-block px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-medium">
                + Nuova Vendita
            </a>
        </div>
    @endif
@endsection
