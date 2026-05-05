@extends('layouts.app-staff')

@section('title', 'Acquisizioni')

@section('content')
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-4xl font-bold text-gray-900">Acquisizioni</h1>
            <p class="text-gray-600 mt-2">Gestione delle acquisizioni di libri dagli studenti</p>
        </div>
        <a href="{{ route('staff.acquisitions.create') }}" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium">
            + Nuova Acquisizione
        </a>
    </div>

    @if($acquisitions->count() > 0)
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Venditore</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Staff</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Libri</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Totale</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Stato</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Data</th>
                            <th class="px-6 py-3 text-center text-sm font-semibold text-gray-900">Azioni</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($acquisitions as $acquisition)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 text-sm text-gray-900 font-medium">
                                    <div>
                                        <p class="font-semibold">{{ $acquisition->seller->name }} {{ $acquisition->seller->surname }}</p>
                                        <p class="text-xs text-gray-500">{{ $acquisition->seller->code }}</p>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    {{ $acquisition->staff->name }} {{ $acquisition->staff->surname }}
                                </td>
                                <td class="px-6 py-4 text-sm font-semibold text-gray-900">
                                    {{ $acquisition->bookListings->count() }}
                                </td>
                                <td class="px-6 py-4 text-sm font-semibold text-gray-900">
                                    €{{ number_format($acquisition->total_price, 2) }}
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    <span class="px-3 py-1 rounded-full text-xs font-medium
                                        @if($acquisition->status === 'completed')
                                            bg-green-50 text-green-700
                                        @elseif($acquisition->status === 'pending')
                                            bg-yellow-50 text-yellow-700
                                        @else
                                            bg-red-50 text-red-700
                                        @endif
                                    ">
                                        @switch($acquisition->status)
                                            @case('completed')
                                                Completata
                                                @break
                                            @case('pending')
                                                In Sospeso
                                                @break
                                            @case('rejected')
                                                Rifiutata
                                                @break
                                            @default
                                                {{ ucfirst($acquisition->status) }}
                                        @endswitch
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    {{ $acquisition->created_at->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <a href="{{ route('staff.acquisitions.show', $acquisition->id) }}" class="px-4 py-2 bg-blue-600 text-white text-xs font-medium rounded-lg hover:bg-blue-700 transition inline-block">
                                        👁️ Visualizza
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4 border-t border-gray-200">
                {{ $acquisitions->links() }}
            </div>
        </div>
    @else
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-12 text-center">
            <div class="text-5xl mb-4">📚</div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Nessuna acquisizione</h3>
            <p class="text-gray-600 mb-6">Non hai ancora acquisito libri. Inizia ad aggiungerne!</p>
            <a href="{{ route('staff.acquisitions.create') }}" class="inline-block px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium">
                + Nuova Acquisizione
            </a>
        </div>
    @endif
@endsection
