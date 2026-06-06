@extends('layouts.app-staff')

@section('title', 'Gestione Utenti')

@section('content')
    <div class="mb-8 flex justify-between items-start">
        <div>
            <h1 class="text-4xl font-bold text-gray-900">Gestione Utenti</h1>
            <p class="text-gray-600 mt-2">Gestisci studenti e staff della tua scuola</p>
        </div>
        <a href="{{ route('staff.users.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition">
            + Aggiungi utente
        </a>
    </div>

    <!-- Filter Cards - Students and Staff -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <!-- All Users Card -->
        <a href="{{ route('staff.users.index') }}" class="transform transition hover:scale-105">
            <div class="bg-gradient-to-br from-gray-500 to-gray-600 text-white rounded-xl shadow-lg p-8 cursor-pointer hover:shadow-xl">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-100 font-medium uppercase text-sm mb-2">Tutti</p>
                        <p class="text-5xl font-bold">{{ $studentsCount + $staffCount }}</p>
                    </div>
                    <svg class="w-20 h-20 opacity-30" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/>
                    </svg>
                </div>
                <p class="text-gray-100 mt-4 text-sm">{{ empty($type) ? 'Visualizzando...' : 'Clicca per visualizzare' }}</p>
            </div>
        </a>

        <!-- Students Card -->
        <a href="{{ route('staff.users.index', ['type' => 'studente']) }}" class="transform transition hover:scale-105">
            <div class="bg-gradient-to-br from-blue-500 to-blue-600 text-white rounded-xl shadow-lg p-8 cursor-pointer hover:shadow-xl">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-blue-100 font-medium uppercase text-sm mb-2">Studenti</p>
                        <p class="text-5xl font-bold">{{ $studentsCount }}</p>
                    </div>
                    <svg class="w-20 h-20 opacity-30" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                    </svg>
                </div>
                <p class="text-blue-100 mt-4 text-sm">{{ $type === 'studente' ? 'Visualizzando...' : 'Clicca per visualizzare' }}</p>
            </div>
        </a>

        <!-- Staff Card -->
        <a href="{{ route('staff.users.index', ['type' => 'staff']) }}" class="transform transition hover:scale-105">
            <div class="bg-gradient-to-br from-purple-500 to-purple-600 text-white rounded-xl shadow-lg p-8 cursor-pointer hover:shadow-xl">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-purple-100 font-medium uppercase text-sm mb-2">Staff</p>
                        <p class="text-5xl font-bold">{{ $staffCount }}</p>
                    </div>
                    <svg class="w-20 h-20 opacity-30" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/>
                    </svg>
                </div>
                <p class="text-purple-100 mt-4 text-sm">{{ $type === 'staff' ? 'Visualizzando...' : 'Clicca per visualizzare' }}</p>
            </div>
        </a>
    </div>

    @if($users->count() > 0)
        <!-- Filter Form -->
        <div class="bg-white border border-blue-200 rounded-lg p-6 mb-8">
            <form method="GET" action="{{ route('staff.users.index') }}" class="flex gap-2">
                <input type="hidden" name="type" value="{{ $type }}">
                <input 
                    type="text" 
                    name="q" 
                    placeholder="Filtra per cognome, email o codice..." 
                    value="{{ $query }}"
                    class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    autocomplete="off"
                />
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition">
                    Filtra
                </button>
                @if($query)
                    <a href="{{ route('staff.users.index', ['type' => $type]) }}" class="px-6 py-2 bg-gray-500 text-white font-medium rounded-lg hover:bg-gray-600 transition">
                        Reset
                    </a>
                @endif
            </form>
            @if($query)
                <p class="text-sm text-gray-600 mt-2">Risultati per: <strong>{{ $query }}</strong> ({{ $users->total() }} risultati)</p>
            @endif
        </div>
    @endif

    <!-- Users Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        @if ($users->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200">
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Nome</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Cognome</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Email</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Codice</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Ruolo</th>
                            <th class="px-6 py-3 text-right text-sm font-semibold text-gray-700">Azioni</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $user)
                            <tr class="border-b border-gray-200 hover:bg-gray-50 transition">
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $user->name }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $user->surname }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $user->email }}</td>
                                <td class="px-6 py-4 text-sm font-mono text-gray-700 bg-gray-50 rounded px-2 py-1 inline-block">{{ $user->code }}</td>
                                <td class="px-6 py-4 text-sm">
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $user->role === 'studente' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                                        {{ $user->role === 'studente' ? 'Studente' : ucfirst($user->role) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right text-sm space-x-2">
                                    @if($user->role === 'studente')
                                        <a href="{{ route('staff.user-history.show', $user) }}" class="text-green-600 hover:text-green-900 font-medium">
                                            Storico
                                        </a>
                                    @endif
                                    <a href="{{ route('staff.users.edit', $user) }}" class="text-blue-600 hover:text-blue-900 font-medium">
                                        Modifica
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if ($users->hasPages())
                <div class="bg-gray-50 border-t border-gray-200 px-6 py-4">
                    {{ $users->links() }}
                </div>
            @endif
        @else
            <div class="p-8 text-center">
                <p class="text-gray-600 text-lg">Nessun utente trovato{{ !empty($type) ? ($type === 'studente' ? ' tra gli studenti' : ' tra lo staff') : '' }}{{ $query ? ' per "' . $query . '"' : '' }}</p>
                @if($query || !empty($type))
                    <a href="{{ route('staff.users.index') }}" class="text-blue-600 hover:text-blue-900 mt-2 inline-block">
                        Azzera i filtri
                    </a>
                @endif
            </div>
        @endif
    </div>
@endsection
