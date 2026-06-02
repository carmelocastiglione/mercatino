@extends('layouts.app-staff')

@section('title', 'Riscossioni')

@section('content')
    <div class="mb-8">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-4xl font-bold text-gray-900">Riscossioni</h1>
                <p class="text-gray-600 mt-2">Gestione delle riscossioni degli studenti</p>
            </div>
            <a href="{{ route('staff.withdrawals.create') }}" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition font-medium">
                + Nuova Riscossione
            </a>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <x-stats-card label="Totale Ricavi Vendite" :value="$totalEarned" color="green" formatted />
            <x-stats-card label="Totale Da Riscuotere" :value="$totalAvailable" color="blue" formatted />
            <x-stats-card label="Totale Riscosso" :value="$totalWithdrawn" color="red" formatted />
        </div>

        <!-- Progress Bar -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-8">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-lg font-semibold text-gray-900">Progresso Riscossioni</h3>
                <span class="text-sm font-medium text-gray-600">{{ $usersWithdrawn }} / {{ $usersWithSoldBooks }} utenti</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-3">
                <div class="bg-gradient-to-r from-green-500 to-green-600 h-3 rounded-full transition-all duration-300" style="width: {{ $withdrawalProgress }}%"></div>
            </div>
            <p class="text-sm text-gray-600 mt-3">{{ round($withdrawalProgress) }}% degli utenti con libri venduti ha ritirato i propri guadagni</p>
        </div>

        @if(session('success'))
            <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                {{ session('success') }}
            </div>
        @endif

        <!-- Sellers Table -->
        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <table class="w-full">
                <thead class="bg-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Venditore</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Email</th>
                        <th class="px-6 py-3 text-right text-sm font-semibold text-gray-900">Totale Vendite</th>
                        <th class="px-6 py-3 text-right text-sm font-semibold text-gray-900">Riscosso</th>
                        <th class="px-6 py-3 text-right text-sm font-semibold text-gray-900">Saldo Disponibile</th>
                        <th class="px-6 py-3 text-center text-sm font-semibold text-gray-900">Azioni</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($sellers as $seller)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm text-gray-900">
                                <strong>{{ $seller->name }} {{ $seller->surname }}</strong>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ $seller->email }}
                            </td>
                            <td class="px-6 py-4 text-right text-sm text-gray-900">
                                <strong class="text-green-600">{{ number_format($seller->getTotalSalesAmount(), 2, ',', '.') }}€</strong>
                            </td>
                            <td class="px-6 py-4 text-right text-sm text-gray-900">
                                <strong class="text-red-600">{{ number_format($seller->getTotalWithdrawnAmount(), 2, ',', '.') }}€</strong>
                            </td>
                            <td class="px-6 py-4 text-right text-sm">
                                @php
                                    $balance = $seller->getAvailableBalance();
                                @endphp
                                <strong class="@if($balance > 0) text-blue-600 @else text-gray-500 @endif">
                                    {{ number_format($balance, 2, ',', '.') }}€
                                </strong>
                            </td>
                            <td class="px-6 py-4 text-center text-sm">
                                @if($balance > 0)
                                    <a href="{{ route('staff.withdrawals.create', ['user_id' => $seller->id]) }}" class="text-indigo-600 hover:text-indigo-900 font-semibold">
                                        Ritira
                                    </a>
                                @else
                                    <span class="text-gray-500">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                Nessun venditore con vendite trovato
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-6">
            {{ $sellers->links() }}
        </div>

        <!-- Recent Withdrawals -->
        <div class="mt-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-4">Ultimi Prelievi</h2>
            <div class="bg-white shadow-md rounded-lg overflow-hidden">
                <table class="w-full">
                    <thead class="bg-gray-200">
                        <tr>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Venditore</th>
                            <th class="px-6 py-3 text-right text-sm font-semibold text-gray-900">Importo</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Note</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Data</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @php
                            $recentWithdrawals = \App\Models\Withdrawal::with('user')->latest()->take(10)->get();
                        @endphp
                        @forelse($recentWithdrawals as $withdrawal)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    <strong>{{ $withdrawal->user->name }} {{ $withdrawal->user->surname }}</strong>
                                </td>
                                <td class="px-6 py-4 text-right text-sm text-gray-900">
                                    <strong class="text-green-600">{{ number_format($withdrawal->amount, 2, ',', '.') }}€</strong>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    {{ $withdrawal->notes ?? '-' }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    {{ $withdrawal->created_at->format('d/m/Y H:i') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-4 text-center text-gray-500">
                                    Nessun prelievo registrato
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
