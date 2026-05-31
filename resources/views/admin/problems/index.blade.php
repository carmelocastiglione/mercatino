@extends('layouts.app-dashboard')

@section('title', 'Gestione Problemi')

@section('dashboard-content')
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <a href="{{ route('admin.dashboard') }}" class="text-blue-600 hover:text-blue-800 font-medium">← Torna al Dashboard</a>
                <h1 class="text-4xl font-bold text-gray-900 mt-4">Problemi Segnalati</h1>
                <p class="text-gray-600 mt-2">Gestisci i feedback degli utenti</p>
            </div>
            <div class="text-right">
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <p class="text-yellow-800 font-bold text-2xl">
                        {{ $problems->where('status', 'pending')->count() }}
                    </p>
                    <p class="text-yellow-700 text-sm">Da esaminare</p>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-6 bg-green-50 border border-green-200 rounded-lg p-4">
            <p class="text-green-800 font-medium">{{ session('success') }}</p>
        </div>
    @endif

    @if($problems->count() === 0)
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-12 text-center">
            <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <p class="text-gray-600 text-lg">Nessun problema da visualizzare</p>
        </div>
    @else
        <div class="space-y-4">
            @foreach($problems as $problem)
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-2">
                                <h3 class="text-lg font-bold text-gray-900">
                                    Segnalazione #{{ $problem->id }}
                                </h3>
                                @if($problem->status === 'pending')
                                    <span class="inline-block bg-yellow-100 text-yellow-800 text-xs font-bold rounded-full px-3 py-1">
                                        Da esaminare
                                    </span>
                                @elseif($problem->status === 'resolved')
                                    <span class="inline-block bg-green-100 text-green-800 text-xs font-bold rounded-full px-3 py-1">
                                        ✓ Corretto
                                    </span>
                                @endif
                            </div>
                            <p class="text-sm text-gray-600">
                                <strong>Utente:</strong> {{ $problem->user->name }} {{ $problem->user->surname }}
                                <span class="text-gray-500">({{ $problem->user->code }})</span>
                            </p>
                            <p class="text-sm text-gray-600">
                                <strong>Ruolo:</strong>
                                @switch($problem->user->role)
                                    @case('studente')
                                        Studente
                                        @break
                                    @case('staff')
                                        Staff
                                        @break
                                    @case('admin')
                                        Amministratore
                                        @break
                                    @default
                                        Sconosciuto
                                @endswitch
                            </p>
                            <p class="text-sm text-gray-500 mt-1">
                                Segnalato: {{ $problem->created_at->format('d/m/Y H:i') }}
                            </p>
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="bg-gray-50 rounded-lg p-4 mb-4 border border-gray-200">
                        <p class="text-sm font-semibold text-gray-900 mb-2">Descrizione del problema:</p>
                        <p class="text-gray-700 whitespace-pre-wrap">{{ $problem->description }}</p>
                    </div>

                    <!-- Actions -->
                    @if($problem->status === 'pending')
                        <div class="flex gap-2">
                            <form action="{{ route('admin.problems.resolve', $problem) }}" method="POST" class="inline">
                                @csrf
                                @method('PUT')
                                <button
                                    type="submit"
                                    class="px-4 py-2 bg-green-600 text-white text-sm font-medium rounded hover:bg-green-700 transition"
                                >
                                    ✓ Marca come Corretto
                                </button>
                            </form>
                            <form action="{{ route('admin.problems.delete', $problem) }}" method="POST" class="inline">
                                @csrf
                                @method('PUT')
                                <button
                                    type="submit"
                                    onclick="return confirm('Sei sicuro di voler eliminare questa segnalazione?')"
                                    class="px-4 py-2 bg-red-600 text-white text-sm font-medium rounded hover:bg-red-700 transition"
                                >
                                    🗑 Elimina
                                </button>
                            </form>
                        </div>
                    @else
                        <div class="text-gray-600 text-sm">
                            <p>
                                @if($problem->status === 'resolved')
                                    ✓ Questo problema è stato marcato come corretto
                                @else
                                    Questo problema è stato eliminato
                                @endif
                            </p>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        @if($problems->hasPages())
            <div class="mt-6">
                {{ $problems->links() }}
            </div>
        @endif
    @endif
@endsection
