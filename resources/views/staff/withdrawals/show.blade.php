@extends('layouts.app-staff')

@section('content')
<div class="max-w-2xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="px-4 py-6 sm:px-0">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-900">Dettagli Riscossione</h1>
            <a href="{{ route('staff.withdrawals.index') }}" class="text-indigo-600 hover:text-indigo-900 font-semibold">
                ← Torna alla lista
            </a>
        </div>

        <!-- Withdrawal Details Card -->
        <div class="bg-white shadow-md rounded-lg p-6 mb-6">
            <div class="grid grid-cols-2 gap-6">
                <!-- Seller Info -->
                <div>
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Venditore</h2>
                    <div class="space-y-2">
                        <div>
                            <p class="text-sm text-gray-600">Nome</p>
                            <p class="text-gray-900 font-semibold">{{ $withdrawal->user->name }} {{ $withdrawal->user->surname }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Email</p>
                            <p class="text-gray-900">{{ $withdrawal->user->email }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Codice</p>
                            <p class="text-gray-900">{{ $withdrawal->user->code ?? '-' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Withdrawal Info -->
                <div>
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Informazioni Riscossione</h2>
                    <div class="space-y-2">
                        <div>
                            <p class="text-sm text-gray-600">Importo</p>
                            <p class="text-2xl font-bold text-green-600">{{ number_format($withdrawal->amount, 2, ',', '.') }}€</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Data</p>
                            <p class="text-gray-900">{{ $withdrawal->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Registrato da</p>
                            <p class="text-gray-900">Staff Mercatino</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Notes Section -->
            @if($withdrawal->notes)
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Note</h3>
                    <p class="text-gray-700 whitespace-pre-wrap">{{ $withdrawal->notes }}</p>
                </div>
            @endif
        </div>

        <!-- Seller Sales Summary -->
        <div class="bg-white shadow-md rounded-lg p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Riepilogo Finanziario Venditore</h2>
            <div class="space-y-3">
                <div class="flex justify-between items-center pb-3 border-b border-gray-200">
                    <span class="text-gray-600">Totale Vendite</span>
                    <span class="text-lg font-semibold text-green-600">{{ number_format($withdrawal->user->getTotalSalesAmount(), 2, ',', '.') }}€</span>
                </div>
                <div class="flex justify-between items-center pb-3 border-b border-gray-200">
                    <span class="text-gray-600">Totale Riscosso</span>
                    <span class="text-lg font-semibold text-red-600">{{ number_format($withdrawal->user->getTotalWithdrawnAmount(), 2, ',', '.') }}€</span>
                </div>
                <div class="flex justify-between items-center pt-3 bg-blue-50 p-3 rounded">
                    <span class="text-gray-900 font-semibold">Saldo Disponibile</span>
                    <span class="text-lg font-bold text-blue-600">{{ number_format($withdrawal->user->getAvailableBalance(), 2, ',', '.') }}€</span>
                </div>
            </div>
        </div>

        <!-- Recent Withdrawals of This Seller -->
        <div class="bg-white shadow-md rounded-lg p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Riscossioni Precedenti - {{ $withdrawal->user->name }} {{ $withdrawal->user->surname }}</h2>
            
            @php
                $otherWithdrawals = $withdrawal->user->withdrawals()->where('id', '!=', $withdrawal->id)->latest()->get();
            @endphp

            @if($otherWithdrawals->count() > 0)
                <table class="w-full">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-4 py-2 text-left text-sm font-semibold text-gray-900">Importo</th>
                            <th class="px-4 py-2 text-left text-sm font-semibold text-gray-900">Data</th>
                            <th class="px-4 py-2 text-left text-sm font-semibold text-gray-900">Note</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($otherWithdrawals as $w)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm font-semibold text-green-600">{{ number_format($w->amount, 2, ',', '.') }}€</td>
                                <td class="px-4 py-3 text-sm text-gray-600">{{ $w->created_at->format('d/m/Y H:i') }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600">{{ $w->notes ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p class="text-gray-500 text-center py-4">Nessun'altra riscossione registrata per questo venditore</p>
            @endif
        </div>

        <!-- Action Buttons -->
        <div class="flex gap-3 mt-6">
            <a href="{{ route('staff.withdrawals.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-900 font-bold py-2 px-6 rounded">
                Torna alla lista
            </a>
        </div>
    </div>
</div>
@endsection
