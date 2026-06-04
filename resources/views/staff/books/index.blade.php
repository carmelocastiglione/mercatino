@extends('layouts.app-staff')

@section('title', 'Libri in Catalogo')

@section('content')
    <div class="mb-8 flex justify-between items-start">
        <div>
            <h1 class="text-4xl font-bold text-gray-900">Libri in Catalogo</h1>
            <p class="text-gray-600 mt-2">Gestisci i libri della tua scuola</p>
        </div>
        <a href="{{ route('staff.books.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition">
            + Aggiungi libro
        </a>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <x-stats-card label="Libri Totali" :value="$totalBooks" color="blue" />
        <x-stats-card label="Copie Medie Disponibili Per Libro" :value="round($avgAvailableCopies, 1)" color="green" />
        <x-stats-card label="Copie Medie Totali Per Libro" :value="round($avgTotalCopies, 1)" color="purple" />
    </div>

    @if ($message = Session::get('success'))
        <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg text-green-800">
            {{ $message }}
        </div>
    @endif

    @if ($message = Session::get('error'))
        <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg text-red-800">
            {{ $message }}
        </div>
    @endif

    @if($books->count() > 0)
        <!-- Filter Form -->
        <div class="bg-white border border-blue-200 rounded-lg p-6 mb-8">
            <form method="GET" action="{{ route('staff.books.index') }}" class="flex gap-2">
                <input 
                    type="text" 
                    name="q" 
                    placeholder="Filtra per titolo, autore o codice ISBN..." 
                    value="{{ $filterQuery }}"
                    class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    autocomplete="off"
                />
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition">
                    Filtra
                </button>
                @if($filterQuery)
                    <a href="{{ route('staff.books.index') }}" class="px-6 py-2 bg-gray-500 text-white font-medium rounded-lg hover:bg-gray-600 transition">
                        Reset
                    </a>
                @endif
            </form>
            @if($filterQuery)
                <p class="text-sm text-gray-600 mt-2">Risultati per: <strong>{{ $filterQuery }}</strong> ({{ $books->total() }} risultati)</p>
            @endif
        </div>
    @endif

    <!-- Books Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        @if ($books->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200">
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Titolo</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Autore</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Materia</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Classe</th>
                            <th class="px-6 py-3 text-center text-sm font-semibold text-gray-700">Copie disponibili</th>
                            <th class="px-6 py-3 text-center text-sm font-semibold text-gray-700">Prezzo</th>
                            <th class="px-6 py-3 text-right text-sm font-semibold text-gray-700">Azioni</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($books as $book)
                            <tr class="border-b border-gray-200 hover:bg-gray-50 transition">
                                <td class="px-6 py-4">
                                    <div>
                                        <p class="font-medium text-gray-900">{{ $book->title }}</p>
                                        @if ($book->isbn)
                                            <p class="text-xs text-gray-500">ISBN: {{ $book->isbn }}</p>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-700">{{ $book->author ?? '—' }}</td>
                                <td class="px-6 py-4 text-sm text-gray-700">{{ $book->subject ?? '—' }}</td>
                                <td class="px-6 py-4 text-sm text-gray-700">{{ $book->school_class ?? '—' }}</td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center justify-center px-3 py-1 rounded-full text-sm font-medium bg-blue-50 text-blue-700">
                                        {{ $book->listings_count }} / {{ $book->total_listings }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center text-sm text-gray-700">
                                    @if ($book->original_price)
                                        €{{ number_format($book->original_price, 2, ',', '.') }}
                                    @else
                                        —
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route('staff.books.edit', $book) }}" class="text-blue-600 hover:text-blue-800 font-medium text-sm">
                                            Modifica
                                        </a>
                                        <form action="{{ route('staff.books.destroy', $book) }}" method="POST" style="display: inline;" onsubmit="return confirm('Sei sicuro? Puoi eliminare solo libri senza copie in catalogo.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-800 font-medium text-sm">
                                                Elimina
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                {{ $books->links() }}
            </div>
        @else
            <div class="p-12 text-center">
                <p class="text-gray-500 text-lg mb-4">Nessun libro nel catalogo</p>
                <a href="{{ route('staff.books.create') }}" class="text-blue-600 hover:text-blue-800 font-medium">
                    Inizia ad aggiungere libri →
                </a>
            </div>
        @endif
    </div>
@endsection
