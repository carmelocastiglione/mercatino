@extends('layouts.app-staff')

@section('title', 'Acquisizioni')

@section('content')
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-4xl font-bold text-gray-900">Acquisizioni</h1>
            <p class="text-gray-600 mt-2">Libri disponibili per la vendita</p>
        </div>
        <a href="{{ route('staff.listings.create') }}" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium">
            + Acquisisci Libro
        </a>
    </div>

    @if(session('success'))
        <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    @if($listings->count() > 0)
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Libro</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Condizione</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Prezzo</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Acquisito da</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Azioni</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($listings as $listing)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 text-sm text-gray-900 font-medium">
                                    <div>
                                        <p class="font-semibold">{{ $listing->book->title }}</p>
                                        <p class="text-xs text-gray-500">{{ $listing->book->author }}</p>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    <span class="px-3 py-1 bg-blue-50 text-blue-700 rounded-full text-xs font-medium">
                                        @switch($listing->condition)
                                            @case('like-new')
                                                Come Nuovo
                                                @break
                                            @case('good')
                                                Buona
                                                @break
                                            @case('fair')
                                                Discreta
                                                @break
                                            @case('poor')
                                                Scarsa
                                                @break
                                            @default
                                                {{ ucfirst($listing->condition) }}
                                        @endswitch
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm font-semibold text-gray-900">
                                    €{{ number_format($listing->price, 2) }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    {{ $listing->seller->name }}
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    <form action="{{ route('staff.listings.mark-sold', $listing->id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" class="text-green-600 hover:text-green-800 font-medium transition">
                                            Vendi
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4 border-t border-gray-200">
                {{ $listings->links() }}
            </div>
        </div>
    @else
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-12 text-center">
            <div class="text-5xl mb-4">📚</div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Nessuna acquisizione</h3>
            <p class="text-gray-600 mb-6">Non hai ancora acquisito libri. Inizia ad aggiungerne!</p>
            <a href="{{ route('staff.listings.create') }}" class="inline-block px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium">
                + Acquisisci Primo Libro
            </a>
        </div>
    @endif
@endsection
