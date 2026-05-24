@extends('layouts.app-student')

@section('title', 'Le Mie Riscossioni')

@section('content')
    <div class="mb-12">
        <h1 class="text-4xl font-bold text-gray-900 mb-2">Le Mie Riscossioni</h1>
        <p class="text-gray-600">Storico di tutte le riscossioni effettuate</p>
    </div>

    <!-- Summary Stats -->
    <div class="bg-gradient-to-br from-orange-50 to-orange-100 rounded-2xl p-8 shadow-sm border-2 border-orange-200 mb-12">
        <p class="text-gray-600 font-semibold text-sm tracking-wide uppercase">Totale Riscossioni</p>
        <p class="mt-3 text-5xl font-black text-orange-600">{{ $totalReclaims }}</p>
    </div>

    <!-- Reclaims Table -->
    @if($reclaims->count() > 0)
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Libro</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Status</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Data Riscossione</th>
                        <th class="px-6 py-3 text-center text-sm font-semibold text-gray-900">Azioni</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($reclaims as $reclaim)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4">
                                <div>
                                    <p class="font-medium text-gray-900">{{ $reclaim->bookListing->book->title }}</p>
                                    <p class="text-sm text-gray-600">{{ $reclaim->bookListing->book->author }}</p>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 rounded-full text-sm font-medium
                                    @switch($reclaim->status)
                                        @case('pending') bg-yellow-100 text-yellow-800 @break
                                        @case('approved') bg-green-100 text-green-800 @break
                                        @case('rejected') bg-red-100 text-red-800 @break
                                    @endswitch
                                ">
                                    @switch($reclaim->status)
                                        @case('pending') In Sospeso @break
                                        @case('approved') Approvata @break
                                        @case('rejected') Rifiutata @break
                                    @endswitch
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ $reclaim->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <a href="{{ route('student.reclaims.show', $reclaim->id) }}" class="text-orange-600 hover:text-orange-900 font-semibold text-sm">
                                    Dettagli
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-6">
            {{ $reclaims->links() }}
        </div>
    @else
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-12 text-center">
            <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <p class="text-gray-600 text-lg mb-6">Non hai ancora nessuna riscossione</p>
            <a href="{{ route('student.dashboard') }}" class="bg-orange-600 text-white px-6 py-3 rounded-lg hover:bg-orange-700 transition font-medium inline-block">
                Torna alla dashboard
            </a>
        </div>
    @endif
@endsection
