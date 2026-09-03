@extends('layouts.app-student')

@section('title', 'I Miei Libri')

@section('content')
    <div class="mb-8">
        <h1 class="text-4xl font-bold text-gray-900 mb-2">I Miei Libri</h1>
        <p class="text-gray-600">Visualizza tutti i libri in vendita e il loro stato</p>
    </div>

    <!-- Status Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
        <x-stats-card label="Disponibili" :value="$statusStats['available']" color="blue" />
        <x-stats-card label="Prenotati" :value="$statusStats['reserved']" color="yellow" />
        <x-stats-card label="Venduti" :value="$statusStats['sold']" color="green" />
    </div>

    <!-- Acquisitions Table -->
    @if($acquisitions->count() > 0)
        <div class="mb-12">
            <h2 class="text-2xl font-bold text-gray-900 mb-4">Libri acquisiti</h2>
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Codice transazione</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Numero Libri</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Prezzo Totale</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Data Acquisizione</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Azioni</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($acquisitions as $acquisition)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4 text-sm font-mono text-gray-600">
                                        {{ $acquisition->ean13 }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-900">
                                        {{ $acquisition->bookListings->count() }}
                                    </td>
                                    <td class="px-6 py-4 text-sm font-semibold text-gray-900">
                                        €{{ number_format($acquisition->total_price, 2) }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600">
                                        {{ $acquisition->created_at->format('d/m/Y H:i') }}
                                    </td>
                                    <td class="px-6 py-4 text-sm">
                                        <a href="{{ route('student.acquisitions.show', $acquisition) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                            Visualizza
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    <!-- Listings Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Titolo</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">ISBN</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Prezzo</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Condizioni</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Aggiunto</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($listings as $listing)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">
                                {{ $listing->book->title }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600 font-mono">
                                {{ $listing->book->isbn }}
                            </td>
                            <td class="px-6 py-4 text-sm font-semibold text-gray-900">
                                €{{ number_format($listing->price, 2) }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                <span class="capitalize">
                                    @switch($listing->condition)
                                        @case('like-new')
                                            Come nuovo
                                        @break
                                        @case('good')
                                            Buono
                                        @break
                                        @case('fair')
                                            Discreto
                                        @break
                                        @case('poor')
                                            Usurato
                                        @break
                                        @default
                                            {{ $listing->condition }}
                                    @endswitch
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                    @if($listing->status === 'available')
                                        bg-blue-100 text-blue-800
                                    @elseif($listing->status === 'reserved')
                                        bg-yellow-100 text-yellow-800
                                    @elseif($listing->status === 'sold')
                                        bg-green-100 text-green-800
                                    @elseif($listing->status === 'withdrawn')
                                        bg-lime-100 text-lime-800
                                    @elseif($listing->status === 'reclaim')
                                        bg-fuchsia-100 text-fuchsia-800
                                    @elseif($listing->status === 'archived')
                                        bg-orange-100 text-orange-800
                                    @else
                                        bg-gray-100 text-gray-800
                                    @endif
                                ">
                                    @switch($listing->status)
                                        @case('available')
                                            Disponibile
                                        @break
                                        @case('reserved')
                                            Prenotato
                                        @break
                                        @case('sold')
                                            Venduto
                                        @break
                                        @case('withdrawn')
                                            Riscosso
                                        @break
                                        @case('reclaim')
                                            Ritirato
                                        @break
                                        @case('archived')
                                            Ceduto
                                        @break
                                        @default
                                            {{ $listing->status }}
                                    @endswitch
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ $listing->created_at->format('d/m/Y') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                <p class="text-sm">Nessun libro trovato</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    @if($listings->hasPages())
        <div class="mt-8">
            {{ $listings->links() }}
        </div>
    @endif
@endsection
