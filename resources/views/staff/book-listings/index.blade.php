@extends('layouts.app-staff')

@section('title', 'Libri Disponibili')

@section('content')
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-4xl font-bold text-gray-900 mb-2">Libri Disponibili</h1>
        <p class="text-gray-600">Visualizza tutti i libri disponibili nel mercatino</p>
    </div>

    <!-- Listings Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Libro</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Venditore</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Condizione</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Prezzo</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse ($listings as $listing)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4">
                                <p class="font-medium text-gray-900">{{ $listing->book->title }}</p>
                                <p class="text-xs text-gray-500 mt-1">{{ $listing->book->author ?? 'N/A' }}</p>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ $listing->seller->name }} {{ $listing->seller->surname }}
                                <p class="text-xs text-gray-500 mt-1">{{ $listing->seller->code }}</p>
                            </td>
                            <td class="px-6 py-4 text-sm">
                                @if ($listing->condition === 'like-new')
                                    <span class="px-3 py-1 text-xs font-semibold text-green-800 bg-green-100 rounded-full">Come nuovo</span>
                                @elseif ($listing->condition === 'good')
                                    <span class="px-3 py-1 text-xs font-semibold text-blue-800 bg-blue-100 rounded-full">Buono</span>
                                @elseif ($listing->condition === 'fair')
                                    <span class="px-3 py-1 text-xs font-semibold text-yellow-800 bg-yellow-100 rounded-full">Accettabile</span>
                                @else
                                    <span class="px-3 py-1 text-xs font-semibold text-red-800 bg-red-100 rounded-full">Rovinato</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm font-semibold text-gray-900">€ {{ number_format($listing->price, 2, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-gray-500">
                                <p class="text-lg">Nessun libro disponibile</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    @if ($listings->hasPages())
        <div class="mt-6">
            {{ $listings->links() }}
        </div>
    @endif
@endsection
