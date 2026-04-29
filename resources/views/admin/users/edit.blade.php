@extends('layouts.app-dashboard')

@section('dashboard-content')
    <!-- Header -->
    <div class="mb-8">
        <a href="{{ route('admin.users.index') }}" class="text-blue-600 hover:text-blue-900 font-medium text-sm mb-4 inline-block">← Torna alla lista</a>
        <h1 class="text-4xl font-bold text-gray-900 mb-2">Modifica Utente</h1>
        <p class="text-gray-600">Aggiorna le informazioni dell'utente</p>
    </div>

    <!-- Form -->
    <div class="max-w-2xl bg-white rounded-lg shadow p-8">
        <form method="POST" action="{{ route('admin.users.update', $user) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Name -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-900 mb-2">Nome</label>
                <input 
                    type="text" 
                    name="name" 
                    id="name" 
                    value="{{ old('name', $user->name) }}"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition @error('name') border-red-500 @enderror"
                    placeholder="es. Marco"
                    required
                >
                @error('name')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Surname -->
            <div>
                <label for="surname" class="block text-sm font-medium text-gray-900 mb-2">Cognome</label>
                <input 
                    type="text" 
                    name="surname" 
                    id="surname" 
                    value="{{ old('surname', $user->surname) }}"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition @error('surname') border-red-500 @enderror"
                    placeholder="es. Rossi"
                    required
                >
                @error('surname')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Email -->
            <div>
                <label for="email" class="block text-sm font-medium text-gray-900 mb-2">Email</label>
                <input 
                    type="email" 
                    name="email" 
                    id="email" 
                    value="{{ old('email', $user->email) }}"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition @error('email') border-red-500 @enderror"
                    placeholder="es. marco@example.com"
                    required
                >
                @error('email')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Role -->
            <div>
                <label for="role" class="block text-sm font-medium text-gray-900 mb-2">Ruolo</label>
                <select 
                    name="role" 
                    id="role"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition @error('role') border-red-500 @enderror"
                    required
                >
                    <option value="studente" @selected(old('role', $user->role) === 'studente')>Studente</option>
                    <option value="staff" @selected(old('role', $user->role) === 'staff')>Staff</option>
                    <option value="admin" @selected(old('role', $user->role) === 'admin')>Amministratore</option>
                </select>
                @error('role')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- School -->
            <div>
                <label for="school_id" class="block text-sm font-medium text-gray-900 mb-2">Scuola</label>
                <select 
                    name="school_id" 
                    id="school_id"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition @error('school_id') border-red-500 @enderror"
                >
                    <option value="">Nessuna scuola (admin)</option>
                    @foreach ($schools as $school)
                        <option value="{{ $school->id }}" @selected(old('school_id', $user->school_id) == $school->id)>{{ $school->name }}</option>
                    @endforeach
                </select>
                @error('school_id')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password (Optional) -->
            <div class="pt-6 border-t border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Cambia Password</h3>
                <p class="text-sm text-gray-600 mb-4">Lascia vuoto se non vuoi cambiarla</p>
                
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-900 mb-2">Nuova Password</label>
                    <input 
                        type="password" 
                        name="password" 
                        id="password"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition @error('password') border-red-500 @enderror"
                        placeholder="Almeno 8 caratteri"
                    >
                    @error('password')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mt-4">
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-900 mb-2">Conferma Password</label>
                    <input 
                        type="password" 
                        name="password_confirmation" 
                        id="password_confirmation"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition"
                        placeholder="Ripeti la password"
                    >
                </div>
            </div>

            <!-- Buttons -->
            <div class="flex gap-4 pt-6 border-t border-gray-200">
                <button 
                    type="submit" 
                    class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-6 rounded-lg transition"
                >
                    Salva Modifiche
                </button>
                <a 
                    href="{{ route('admin.users.index') }}" 
                    class="bg-gray-200 hover:bg-gray-300 text-gray-900 font-medium py-2 px-6 rounded-lg transition"
                >
                    Annulla
                </a>
            </div>
        </form>
    </div>
@endsection
