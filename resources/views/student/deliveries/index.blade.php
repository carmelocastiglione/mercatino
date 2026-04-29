@extends('layouts.app-student')

@section('title', 'Le Mie Consegne')

@section('content')
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-4xl font-bold text-gray-900 mb-2">Le Mie Consegne</h1>
            <p class="text-gray-600">Gestisci le consegne che hai prenotato</p>
        </div>
        <a href="{{ route('student.deliveries.create') }}" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition font-medium">
            + Prenota Consegna
        </a>
    </div>

    @if($deliveries->count() > 0)
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Libro</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Condizioni</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Prezzo</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Status</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Data</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Azioni</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($deliveries as $delivery)
                        <tr class="hover:bg-gray-50 transition">
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
                            <td class="px-6 py-4 font-medium text-gray-900">€ {{ number_format($delivery->price, 2, ',', '.') }}</td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 rounded-full text-sm font-medium
                                    @switch($delivery->status)
                                        @case('pending') bg-yellow-100 text-yellow-800 @break
                                        @case('approved') bg-green-100 text-green-800 @break
                                        @case('rejected') bg-red-100 text-red-800 @break
                                    @endswitch
                                ">
                                    @switch($delivery->status)
                                        @case('pending') In Sospeso @break
                                        @case('approved') Approvata @break
                                        @case('rejected') Rifiutata @break
                                    @endswitch
                                </span>
                                @if($delivery->status === 'rejected' && $delivery->rejection_reason)
                                    <p class="text-sm text-red-600 mt-2">{{ $delivery->rejection_reason }}</p>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ $delivery->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-6 py-4 text-sm space-x-2 flex">
                                @if($delivery->status === 'pending')
                                    <a href="{{ route('student.deliveries.edit', $delivery) }}" class="text-blue-600 hover:text-blue-800 font-medium">
                                        Modifica
                                    </a>
                                    <form action="{{ route('student.deliveries.delete', $delivery) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800 font-medium" onclick="return confirm('Annullare questa consegna?')">
                                            Elimina
                                        </button>
                                    </form>
                                @else
                                    <span class="text-gray-500">—</span>
                                @endif
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
            <p class="text-gray-600 text-lg mb-6">Non hai ancora prenotato nessuna consegna</p>
            <a href="{{ route('student.deliveries.create') }}" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition font-medium inline-block">
                Prenota la tua prima consegna
            </a>
        </div>
    @endif
@endsection
