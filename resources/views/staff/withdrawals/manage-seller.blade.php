@extends('layouts.app-staff')

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="px-4 py-6 sm:px-0">
        <!-- Header -->
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">
                    Gestione Ritiri - {{ $seller->name }} {{ $seller->surname }}
                </h1>
                <p class="text-gray-600 text-sm mt-1">{{ $seller->email }} ({{ $seller->code }})</p>
            </div>
            <a href="{{ route('staff.withdrawals.create') }}" class="bg-gray-600 hover:bg-gray-700 text-white font-semibold py-2 px-4 rounded">
                ← Torna alla lista
            </a>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-3 gap-4 mb-6">
            <div class="bg-green-100 border-l-4 border-green-600 rounded-lg p-6">
                <p class="text-sm text-gray-600 uppercase tracking-wide font-semibold">Totale Vendite</p>
                <p class="text-4xl font-bold text-green-600 mt-2">
                    €{{ number_format($seller->getTotalSalesAmount(), 2, ',', '.') }}
                </p>
            </div>
            <div class="bg-red-100 border-l-4 border-red-600 rounded-lg p-6">
                <p class="text-sm text-gray-600 uppercase tracking-wide font-semibold">Già Riscosso</p>
                <p class="text-4xl font-bold text-red-600 mt-2">
                    €{{ number_format($seller->getTotalWithdrawnAmount(), 2, ',', '.') }}
                </p>
            </div>
            <div class="bg-blue-100 border-l-4 border-blue-600 rounded-lg p-6">
                <p class="text-sm text-gray-600 uppercase tracking-wide font-semibold">Saldo Disponibile</p>
                <p class="text-4xl font-bold text-blue-600 mt-2">
                    €{{ number_format($seller->getAvailableBalance(), 2, ',', '.') }}
                </p>
            </div>
        </div>

        <!-- Messages -->
        @if(session('success'))
            <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6 text-green-700">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6 text-red-700">
                {{ $errors->first('error') }}
            </div>
        @endif

        <!-- Books Venduti Section -->
        <div class="bg-white shadow-md rounded-lg mb-8">
            <div class="border-b border-gray-200 px-6 py-4">
                <h2 class="text-xl font-bold text-gray-900">
                    📚 Libri Venduti <span class="text-sm text-gray-500 font-normal">({{ count($soldBooks) }})</span>
                </h2>
            </div>
            
            @if(count($soldBooks) > 0)
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Titolo</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Autore</th>
                                <th class="px-6 py-3 text-center text-sm font-semibold text-gray-900">Condizione</th>
                                <th class="px-6 py-3 text-right text-sm font-semibold text-gray-900">Prezzo</th>
                                <th class="px-6 py-3 text-center text-sm font-semibold text-gray-900">Azione</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($soldBooks as $book)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 text-sm text-gray-900 font-semibold">{{ $book->book->title }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-600">{{ $book->book->author }}</td>
                                    <td class="px-6 py-4 text-center text-sm">
                                        <span class="px-2 py-1 rounded text-white text-xs font-semibold 
                                            @switch($book->condition)
                                                @case('excellent') bg-green-600 @break
                                                @case('good') bg-blue-600 @break
                                                @case('fair') bg-yellow-600 @break
                                                @case('poor') bg-red-600 @break
                                                @default bg-gray-600
                                            @endswitch">
                                            @switch($book->condition)
                                                @case('excellent') Ottima @break
                                                @case('good') Buona @break
                                                @case('fair') Discreta @break
                                                @case('poor') Cattiva @break
                                                @default {{ $book->condition }}
                                            @endswitch
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right text-sm text-gray-900 font-semibold">
                                        €{{ number_format($book->price, 2, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4 text-center text-sm">
                                        <form action="{{ route('staff.withdrawals.withdraw-money', $book->id) }}" method="POST" style="display: inline;">
                                            @csrf
                                            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white text-xs font-bold py-1 px-3 rounded transition-colors">
                                                Ritira Soldi
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="p-8 text-center text-gray-500">
                    <p class="text-lg">Nessun libro venduto</p>
                </div>
            @endif
        </div>

        <!-- Books Non Venduti Section -->
        <div class="bg-white shadow-md rounded-lg mb-8">
            <div class="border-b border-gray-200 px-6 py-4">
                <h2 class="text-xl font-bold text-gray-900">
                    📖 Libri Non Venduti <span class="text-sm text-gray-500 font-normal">({{ count($unsoldBooks) }})</span>
                </h2>
            </div>
            
            @if(count($unsoldBooks) > 0)
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Titolo</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Autore</th>
                                <th class="px-6 py-3 text-center text-sm font-semibold text-gray-900">Condizione</th>
                                <th class="px-6 py-3 text-center text-sm font-semibold text-gray-900">Stato</th>
                                <th class="px-6 py-3 text-right text-sm font-semibold text-gray-900">Prezzo</th>
                                <th class="px-6 py-3 text-center text-sm font-semibold text-gray-900">Azione</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($unsoldBooks as $book)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 text-sm text-gray-900 font-semibold">{{ $book->book->title }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-600">{{ $book->book->author }}</td>
                                    <td class="px-6 py-4 text-center text-sm">
                                        <span class="px-2 py-1 rounded text-white text-xs font-semibold 
                                            @switch($book->condition)
                                                @case('excellent') bg-green-600 @break
                                                @case('good') bg-blue-600 @break
                                                @case('fair') bg-yellow-600 @break
                                                @case('poor') bg-red-600 @break
                                                @default bg-gray-600
                                            @endswitch">
                                            @switch($book->condition)
                                                @case('excellent') Ottima @break
                                                @case('good') Buona @break
                                                @case('fair') Discreta @break
                                                @case('poor') Cattiva @break
                                                @default {{ $book->condition }}
                                            @endswitch
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center text-sm">
                                        <span class="px-2 py-1 rounded text-white text-xs font-semibold 
                                            @switch($book->status)
                                                @case('available') bg-blue-600 @break
                                                @case('pending') bg-yellow-600 @break
                                                @case('withdrawn') bg-red-600 @break
                                                @default bg-gray-600
                                            @endswitch">
                                            @switch($book->status)
                                                @case('available') Disponibile @break
                                                @case('pending') In Sospeso @break
                                                @case('withdrawn') Ritirato @break
                                                @default {{ $book->status }}
                                            @endswitch
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right text-sm text-gray-900 font-semibold">
                                        €{{ number_format($book->price, 2, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4 text-center text-sm">
                                        <form action="{{ route('staff.withdrawals.withdraw-book', $book->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Sei sicuro di voler ritirare questo libro?');">
                                            @csrf
                                            <button type="submit" class="bg-orange-600 hover:bg-orange-700 text-white text-xs font-bold py-1 px-3 rounded transition-colors">
                                                Ritira Libro
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="p-8 text-center text-gray-500">
                    <p class="text-lg">Nessun libro non venduto</p>
                </div>
            @endif
        </div>

        <!-- Withdrawal History -->
        @if($seller->withdrawals()->exists())
            <div class="bg-white shadow-md rounded-lg p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Storico Ritiri</h2>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-900">Data</th>
                                <th class="px-4 py-3 text-right text-sm font-semibold text-gray-900">Importo</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-900">Note</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($seller->withdrawals()->latest()->limit(10)->get() as $withdrawal)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 text-sm text-gray-900">
                                        {{ $withdrawal->created_at->format('d/m/Y H:i') }}
                                    </td>
                                    <td class="px-4 py-3 text-right text-sm text-gray-900 font-semibold">
                                        €{{ number_format($withdrawal->amount, 2, ',', '.') }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-600">
                                        {{ $withdrawal->notes ?? '-' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
