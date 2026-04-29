@extends('layouts.app-dashboard')

@section('dashboard-content')
    <!-- Header -->
    <div class="mb-8">
        <a href="{{ route('admin.schools.index') }}" class="text-blue-600 hover:text-blue-900 font-medium text-sm mb-4 inline-block">← Torna alla lista</a>
        <h1 class="text-4xl font-bold text-gray-900 mb-2">Modifica Scuola</h1>
        <p class="text-gray-600">Aggiorna le informazioni della scuola</p>
    </div>

    <!-- Form -->
    <div class="max-w-2xl bg-white rounded-lg shadow p-8">
        <form method="POST" action="{{ route('admin.schools.update', $school) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Name -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-900 mb-2">Nome Scuola *</label>
                <input 
                    type="text" 
                    name="name" 
                    id="name" 
                    value="{{ old('name', $school->name) }}"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition @error('name') border-red-500 @enderror"
                    placeholder="es. Liceo Classico Dante Alighieri"
                    required
                >
                @error('name')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- City -->
            <div>
                <label for="city" class="block text-sm font-medium text-gray-900 mb-2">Città</label>
                <input 
                    type="text" 
                    name="city" 
                    id="city" 
                    value="{{ old('city', $school->city) }}"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition"
                    placeholder="es. Roma"
                >
            </div>

            <!-- Address -->
            <div>
                <label for="address" class="block text-sm font-medium text-gray-900 mb-2">Indirizzo</label>
                <input 
                    type="text" 
                    name="address" 
                    id="address" 
                    value="{{ old('address', $school->address) }}"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition"
                    placeholder="es. Via Dante Alighieri 123"
                >
            </div>

            <!-- Email -->
            <div>
                <label for="email" class="block text-sm font-medium text-gray-900 mb-2">Email</label>
                <input 
                    type="email" 
                    name="email" 
                    id="email" 
                    value="{{ old('email', $school->email) }}"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition @error('email') border-red-500 @enderror"
                    placeholder="es. info@scuola.it"
                >
                @error('email')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Phone -->
            <div>
                <label for="phone" class="block text-sm font-medium text-gray-900 mb-2">Telefono</label>
                <input 
                    type="tel" 
                    name="phone" 
                    id="phone" 
                    value="{{ old('phone', $school->phone) }}"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition"
                    placeholder="es. +39 06 1234 5678"
                >
            </div>

            <!-- Description -->
            <div>
                <label for="description" class="block text-sm font-medium text-gray-900 mb-2">Descrizione</label>
                <textarea 
                    name="description" 
                    id="description" 
                    rows="4"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition"
                    placeholder="Descrivi la scuola..."
                >{{ old('description', $school->description) }}</textarea>
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
                    href="{{ route('admin.schools.index') }}" 
                    class="bg-gray-200 hover:bg-gray-300 text-gray-900 font-medium py-2 px-6 rounded-lg transition"
                >
                    Annulla
                </a>
            </div>
        </form>
    </div>
@endsection
