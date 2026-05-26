@extends('layouts.app-staff')

@section('title', 'Date di Consegna')

@section('content')
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-4xl font-bold text-gray-900">Date di Consegna</h1>
            <p class="text-gray-600 mt-2">Configura le date disponibili per le consegne dei libri</p>
        </div>
        <a href="{{ route('staff.delivery-dates.create') }}" class="px-6 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition">
            Aggiungi Data
        </a>
    </div>

    <!-- Success Message -->
    @if (session('success'))
        <div class="mb-6 p-4 bg-green-50 border border-green-300 rounded-lg">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-700">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    <!-- Active Dates Section -->
    <div class="mb-8">
        <h2 class="text-2xl font-bold text-gray-900 mb-4">Date Attive</h2>
        
        @if ($activeDates->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach ($activeDates as $date)
                    <div class="bg-gradient-to-br from-green-50 to-emerald-50 border-2 border-green-200 rounded-lg p-5 hover:shadow-lg transition">
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex-1">
                                <p class="text-sm font-semibold text-green-900">{{ $date->label ?? 'Consegna' }}</p>
                                <p class="text-2xl font-bold text-green-600 mt-1">{{ $date->scheduled_date->format('d/m/Y') }}</p>
                                <p class="text-xs text-green-600 mt-1">{{ $date->scheduled_date->format('H:i') }}</p>
                            </div>
                            <span class="inline-block bg-green-200 text-green-800 text-xs font-bold rounded-full px-3 py-1">✓ Attiva</span>
                        </div>

                        <div class="flex gap-2 pt-3 border-t border-green-200">
                            <a href="{{ route('staff.delivery-dates.edit', $date) }}" class="flex-1 px-3 py-2 text-center text-sm font-medium text-green-700 bg-white border border-green-300 rounded-lg hover:bg-green-50 transition">
                                Modifica
                            </a>
                            <form action="{{ route('staff.delivery-dates.toggle', $date) }}" method="POST" class="flex-1">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="w-full px-3 py-2 text-sm font-medium text-amber-700 bg-white border border-amber-300 rounded-lg hover:bg-amber-50 transition">
                                    Disattiva
                                </button>
                            </form>
                            <form action="{{ route('staff.delivery-dates.destroy', $date) }}" method="POST" class="flex-1" onsubmit="return confirm('Elimina questa data?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-full px-3 py-2 text-sm font-medium text-red-700 bg-white border border-red-300 rounded-lg hover:bg-red-50 transition">
                                    Elimina
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="bg-blue-50 border-2 border-blue-300 rounded-lg p-8 text-center">
                <svg class="h-12 w-12 text-blue-400 mx-auto mb-3" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v4a1 1 0 001 1h12a1 1 0 001-1V6a2 2 0 00-2-2H4zm0 4v4a2 2 0 002 2h8a2 2 0 002-2V8H4z" clip-rule="evenodd" />
                </svg>
                <p class="text-blue-800 font-medium">Nessuna data di consegna attiva</p>
                <p class="text-sm text-blue-600 mt-1">Aggiungi una data per permettere agli studenti di prenotare le consegne</p>
            </div>
        @endif
    </div>

    <!-- Inactive Dates Section -->
    @if ($inactiveDates->count() > 0)
        <div>
            <h2 class="text-2xl font-bold text-gray-900 mb-4">Date Disattivate</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach ($inactiveDates as $date)
                    <div class="bg-gradient-to-br from-gray-50 to-slate-50 border-2 border-gray-300 rounded-lg p-5 opacity-75 hover:shadow-lg transition">
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex-1">
                                <p class="text-sm font-semibold text-gray-600">{{ $date->label ?? 'Consegna' }}</p>
                                <p class="text-2xl font-bold text-gray-400 mt-1">{{ $date->scheduled_date->format('d/m/Y') }}</p>
                                <p class="text-xs text-gray-400 mt-1">{{ $date->scheduled_date->format('H:i') }}</p>
                            </div>
                            <span class="inline-block bg-gray-300 text-gray-700 text-xs font-bold rounded-full px-3 py-1">Disattiva</span>
                        </div>

                        <div class="flex gap-2 pt-3 border-t border-gray-300">
                            <a href="{{ route('staff.delivery-dates.edit', $date) }}" class="flex-1 px-3 py-2 text-center text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                                Modifica
                            </a>
                            <form action="{{ route('staff.delivery-dates.toggle', $date) }}" method="POST" class="flex-1">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="w-full px-3 py-2 text-sm font-medium text-green-700 bg-white border border-green-300 rounded-lg hover:bg-green-50 transition">
                                    Attiva
                                </button>
                            </form>
                            <form action="{{ route('staff.delivery-dates.destroy', $date) }}" method="POST" class="flex-1" onsubmit="return confirm('Elimina questa data?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-full px-3 py-2 text-sm font-medium text-red-700 bg-white border border-red-300 rounded-lg hover:bg-red-50 transition">
                                    Elimina
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
@endsection
