@extends('layouts.app-student')

@section('title', 'Le Mie Riscossioni')

@section('content')
    <div class="mb-12">
        <h1 class="text-4xl font-bold text-gray-900 mb-2">Le Mie Riscossioni</h1>
        <p class="text-gray-600">Gestisci i tuoi ritiri di denaro</p>
    </div>

    <!-- Summary Stats -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-12">
        <x-stats-card label="Totale da Ritirare" :value="$totalToWithdraw" color="orange" formatted />
        <x-stats-card label="Totale Ritirato" :value="$totalWithdrawn" color="orange" formatted />
    </div>

    <!-- Withdrawals Table -->
    @if($withdrawals->count() > 0)
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Libro</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">ISBN</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Importo</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Data Ritiro</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($withdrawals as $withdrawal)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4">
                                @if($withdrawal->bookListing && $withdrawal->bookListing->book)
                                    <p class="font-medium text-gray-900">{{ $withdrawal->bookListing->book->title }}</p>
                                @else
                                    <p class="text-gray-500">-</p>
                                @endif
                            </td>
                            <td class="px-6 py-4 font-mono text-gray-600">
                                @if($withdrawal->bookListing && $withdrawal->bookListing->book)
                                    {{ $withdrawal->bookListing->book->isbn }}
                                @else
                                    <p class="text-gray-500">-</p>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-left">
                                <p class="font-bold text-orange-600">€ {{ number_format($withdrawal->amount, 2, ',', '.') }}</p>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ $withdrawal->created_at->format('d/m/Y H:i') }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-6">
            {{ $withdrawals->links() }}
        </div>
    @else
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-12 text-center">
            <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <p class="text-gray-600 text-lg mb-6">Non hai ancora effettuato riscossioni</p>
            <a href="{{ route('student.dashboard') }}" class="bg-orange-600 text-white px-6 py-3 rounded-lg hover:bg-orange-700 transition font-medium inline-block">
                Torna alla dashboard
            </a>
        </div>
    @endif
@endsection
