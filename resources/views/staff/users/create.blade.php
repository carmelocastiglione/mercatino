@extends('layouts.app-staff')

@section('title', 'Aggiungi Utente')

@section('content')
    <div class="mb-8">
        <h1 class="text-4xl font-bold text-gray-900">Aggiungi Utente</h1>
        <p class="text-gray-600 mt-2">Crea un nuovo account per uno studente o membro dello staff</p>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8 max-w-3xl">
        <form action="{{ route('staff.users.store') }}" method="POST" class="space-y-6">
            @csrf

            <!-- Name -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                    Nome <span class="text-red-600">*</span>
                </label>
                <input 
                    type="text" 
                    id="name" 
                    name="name" 
                    value="{{ old('name') }}"
                    placeholder="Es: Marco"
                    class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('name') border-red-500 @enderror"
                    required
                >
                @error('name')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Surname -->
            <div>
                <label for="surname" class="block text-sm font-medium text-gray-700 mb-2">
                    Cognome <span class="text-red-600">*</span>
                </label>
                <input 
                    type="text" 
                    id="surname" 
                    name="surname" 
                    value="{{ old('surname') }}"
                    placeholder="Es: Rossi"
                    class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('surname') border-red-500 @enderror"
                    required
                >
                @error('surname')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Email -->
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                    Email <span class="text-red-600">*</span>
                </label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    value="{{ old('email') }}"
                    placeholder="Es: marco.rossi@example.com"
                    class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('email') border-red-500 @enderror"
                    required
                >
                @error('email')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Role -->
            <div>
                <label for="role" class="block text-sm font-medium text-gray-700 mb-2">
                    Ruolo <span class="text-red-600">*</span>
                </label>
                <select 
                    id="role" 
                    name="role" 
                    class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('role') border-red-500 @enderror"
                    required
                >
                    <option value="">-- Seleziona un ruolo --</option>
                    <option value="studente" {{ old('role') === 'studente' ? 'selected' : '' }}>Studente</option>
                    <option value="staff" {{ old('role') === 'staff' ? 'selected' : '' }}>Staff</option>
                </select>
                @error('role')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password -->
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                    Password <span class="text-red-600">*</span>
                </label>
                <div class="flex gap-2">
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        placeholder="Minimo 8 caratteri"
                        class="flex-1 px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('password') border-red-500 @enderror"
                        required
                    >
                    <button 
                        type="button" 
                        onclick="generatePassword()" 
                        class="px-4 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition whitespace-nowrap"
                    >
                        🔐 Genera
                    </button>
                </div>
                @error('password')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password Confirmation -->
            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                    Conferma Password <span class="text-red-600">*</span>
                </label>
                <input 
                    type="password" 
                    id="password_confirmation" 
                    name="password_confirmation" 
                    placeholder="Ripeti la password"
                    class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    required
                >
            </div>

            <!-- Action Buttons -->
            <div class="flex gap-4 pt-6 border-t border-gray-200">
                <a href="{{ route('staff.users.index') }}" class="flex-1 px-6 py-3 bg-gray-500 text-white font-medium rounded-lg hover:bg-gray-600 transition text-center">
                    Annulla
                </a>
                <button type="submit" class="flex-1 px-6 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition">
                    Crea Utente
                </button>
            </div>
        </form>
    </div>

    <script>
        function generatePassword() {
            // Genera password casuale di 8 caratteri alfanumerici
            const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
            let password = '';
            for (let i = 0; i < 8; i++) {
                password += chars.charAt(Math.floor(Math.random() * chars.length));
            }
            
            // Compila i campi password
            document.getElementById('password').value = password;
            document.getElementById('password_confirmation').value = password;
            
            // Cambia il tipo di input a 'text' per mostrare la password
            document.getElementById('password').type = 'text';
            document.getElementById('password_confirmation').type = 'text';
        }
    </script>
@endsection
