@extends('layouts.app-student')

@section('title', 'I Miei Libri')

@section('content')
    <div class="mb-8">
        <h1 class="text-4xl font-bold text-gray-900 mb-2">I Miei Libri</h1>
        <p class="text-gray-600">Visualizza tutti i libri in vendita e il loro stato</p>
    </div>

    <!-- Status Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
        <div class="bg-blue-50 rounded-lg p-4 border border-blue-200">
            <p class="text-blue-600 text-sm font-medium">Disponibili</p>
            <p class="text-3xl font-bold text-blue-900">{{ $statusStats['available'] }}</p>
        </div>
        <div class="bg-yellow-50 rounded-lg p-4 border border-yellow-200">
            <p class="text-yellow-600 text-sm font-medium">Prenotati</p>
            <p class="text-3xl font-bold text-yellow-900">{{ $statusStats['reserved'] }}</p>
        </div>
        <div class="bg-green-50 rounded-lg p-4 border border-green-200">
            <p class="text-green-600 text-sm font-medium">Venduti</p>
            <p class="text-3xl font-bold text-green-900">{{ $statusStats['sold'] }}</p>
        </div>
    </div>

    <!-- Listings Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Titolo</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Autore</th>
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
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ $listing->book->author }}
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
