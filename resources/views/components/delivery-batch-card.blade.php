@props(['batch'])

<div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden mb-6">
    <!-- Batch Header -->
    <div class="bg-gradient-to-r from-blue-50 to-blue-100 border-b-2 border-blue-300 px-6 py-4">
        <div class="grid grid-cols-6 gap-4 items-center">
            <!-- Transaction Code -->
            <div>
                <p class="text-xs text-gray-600 font-semibold uppercase">Codice Prenotazione</p>
                <p class="font-mono text-lg font-bold text-blue-600">{{ $batch->ean13 }}</p>
            </div>

            <!-- Number of Books -->
            <div>
                <p class="text-xs text-gray-600 font-semibold uppercase">Libri</p>
                <p class="text-lg font-bold text-gray-900">{{ $batch->deliveries->count() }}</p>
            </div>

            <!-- Total Price -->
            <div>
                <p class="text-xs text-gray-600 font-semibold uppercase">Totale</p>
                <p class="text-lg font-bold text-gray-900">€{{ number_format($batch->deliveries->sum('price'), 2) }}</p>
            </div>

            <!-- Request Date -->
            <div>
                <p class="text-xs text-gray-600 font-semibold uppercase">Richiesta</p>
                <p class="text-sm font-medium text-gray-900">{{ $batch->created_at->format('d/m/Y H:i') }}</p>
            </div>

            <!-- Delivery Date -->
            <div>
                <p class="text-xs text-gray-600 font-semibold uppercase">Consegna</p>
                <p class="text-sm font-medium text-gray-900">
                    @if($batch->scheduledDeliveryDate)
                        {{ $batch->scheduledDeliveryDate->scheduled_date->format('d/m/Y') }}
                    @else
                        <span class="text-gray-400">—</span>
                    @endif
                </p>
            </div>

            <!-- Actions -->
            <div class="flex justify-end gap-2">
                <a href="{{ route('student.batches.show', $batch) }}" class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition">
                    Visualizza
                </a>
                @if($batch->status === 'pending')
                    <form action="{{ route('student.batches.delete', $batch) }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 transition" onclick="return confirm('Eliminare questo batch? Tutti i libri verranno rimossi.')">
                            Elimina
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>

    <!-- Books List -->
    <div class="overflow-x-auto">
        <table class="min-w-full">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">#</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Titolo</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">ISBN</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Condizioni</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Prezzo</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($batch->deliveries as $index => $delivery)
                    <tr class="hover:bg-gray-50 transition border-b border-gray-200">
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $index + 1 }}</td>
                        <td class="px-6 py-4">
                            <p class="font-medium text-gray-900">{{ $delivery->book->title }}</p>
                        </td>
                        <td class="px-6 py-4 text-sm font-mono text-gray-600">
                            {{ $delivery->book->isbn ?? '—' }}
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
                                @switch($delivery->condition)
                                    @case('like-new') Come nuovo @break
                                    @case('good') Buono @break
                                    @case('fair') Discreto @break
                                    @case('poor') Scarso @break
                                @endswitch
                            </span>
                        </td>
                        <td class="px-6 py-4 font-medium text-gray-900">€{{ number_format($delivery->price, 2, ',', '.') }}</td>
                        <td class="px-6 py-4">
                            <span class="px-3 py-1 rounded-full text-sm font-medium
                                @switch($delivery->status)
                                    @case('pending') bg-yellow-100 text-yellow-800 @break
                                    @case('approved') bg-green-100 text-green-800 @break
                                    @case('rejected') bg-red-100 text-red-800 @break
                                @endswitch
                            ">
                                @switch($delivery->status)
                                    @case('pending') In attesa @break
                                    @case('approved') Approvata @break
                                    @case('rejected') Rifiutata @break
                                @endswitch
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">Nessun libro in questo batch</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
