@extends('layouts.app-staff')

@section('title', 'Studenti in Attesa di Ritiro')

@section('content')
    <div class="mb-8">
        <div class="mb-8">
            <a href="{{ route('staff.withdrawals.index') }}" class="text-indigo-600 hover:text-indigo-900 font-semibold mb-4 inline-block">
                ← Torna a Riscossioni
            </a>
            <h1 class="text-4xl font-bold text-gray-900">Studenti in Attesa di Ritiro</h1>
            <p class="text-gray-600 mt-2">Lista degli studenti che devono ritirare guadagni e/o libri invenduti</p>
        </div>

        <!-- Summary Card -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-8">
            <div class="text-center">
                <p class="text-sm text-gray-600 mb-2">Studenti in Attesa</p>
                <p class="text-4xl font-bold text-blue-600">{{ count($pendingUsers) }}</p>
            </div>
        </div>

        @if(count($pendingUsers) > 0)
            <!-- Pending Users Table -->
            <div class="bg-white shadow-md rounded-lg overflow-hidden">
                <table class="w-full">
                    <thead class="bg-gray-200">
                        <tr>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Studente</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Codice</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Email</th>
                            <th class="px-6 py-3 text-right text-sm font-semibold text-gray-900">Libri venduti da riscuotere</th>
                            <th class="px-6 py-3 text-right text-sm font-semibold text-gray-900">Libri invenduti da ritirare</th>
                            <th class="px-6 py-3 text-center text-sm font-semibold text-gray-900">Azione</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($pendingUsers as $user)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    <strong>{{ $user->name }} {{ $user->surname }}</strong>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    {{ $user->code }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    {{ $user->email }}
                                </td>
                                <td class="px-6 py-4 text-right text-sm">
                                    @if($user->pendingWithdrawal > 0)
                                        <span class="inline-block bg-blue-100 text-blue-800 text-xs font-semibold px-3 py-1 rounded-full">
                                            {{ $user->pendingWithdrawal }}
                                        </span>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right text-sm">
                                    @if($user->pendingBooks > 0)
                                        <span class="inline-block bg-yellow-100 text-yellow-800 text-xs font-semibold px-3 py-1 rounded-full">
                                            {{ $user->pendingBooks }}
                                        </span>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center text-sm">
                                    <a href="{{ route('staff.withdrawals.process-seller', $user->id) }}" class="text-indigo-600 hover:text-indigo-900 font-semibold">
                                        Visualizza
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <!-- Empty State -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-12 text-center">
                <p class="text-gray-600 text-lg">✓ Tutti gli studenti hanno ritirato i loro guadagni!</p>
            </div>
        @endif
    </div>
@endsection
