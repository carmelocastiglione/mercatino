@extends('layouts.app-staff')

@section('title', 'Consegne da Approvare')

@section('content')
    <div class="mb-8">
        <h1 class="text-4xl font-bold text-gray-900">Consegne da Approvare</h1>
        <p class="text-gray-600 mt-2">Esamina e approva le consegne dei libri dagli studenti</p>
    </div>

    @if($deliveries->count() > 0)
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Studente</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Libro</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Condizioni</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Prezzo</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Data</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Azioni</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($deliveries as $delivery)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4">
                                <div>
                                    <p class="font-medium text-gray-900">{{ $delivery->user->name }}</p>
                                    <p class="text-sm text-gray-600">{{ $delivery->user->email }}</p>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div>
                                    <p class="font-medium text-gray-900">{{ $delivery->book->title }}</p>
                                    <p class="text-sm text-gray-600">{{ $delivery->book->author ?? 'Autore sconosciuto' }}</p>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 rounded-full text-sm font-medium
                                    @switch($delivery->condition)
                                        @case('like-new') bg-green-100 text-green-800 @break
                                        @case('good') bg-blue-100 text-blue-800 @break
                                        @case('fair') bg-yellow-100 text-yellow-800 @break
                                        @case('poor') bg-red-100 text-red-800 @break
                                    @endswitch
                                ">
                                    {{ ucfirst(str_replace('-', ' ', $delivery->condition)) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 font-medium text-gray-900">€ {{ number_format($delivery->price, 0) }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ $delivery->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-6 py-4 text-sm space-x-2 flex">
                                <a href="{{ route('staff.deliveries.show', $delivery) }}" class="text-blue-600 hover:text-blue-800 font-medium">
                                    Rivedi
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-6">
            {{ $deliveries->links() }}
        </div>
    @else
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-12 text-center">
            <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <p class="text-gray-600 text-lg">Nessuna consegna in attesa di approvazione</p>
            <p class="text-gray-500 text-sm mt-2">Torna qui quando gli studenti ne avranno prenotate</p>
        </div>
    @endif
@endsection
