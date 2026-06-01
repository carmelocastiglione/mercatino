@props(['batch'])

<div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
    <div class="px-8 py-6 border-b border-gray-200 bg-gray-50">
        <h2 class="text-2xl font-bold text-gray-900">Libri Prenotati</h2>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900">#</th>
                    <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900">Titolo</th>
                    <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900">Codice</th>
                    <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900">ISBN</th>
                    <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900">Condizione</th>
                    <th class="px-6 py-4 text-right text-sm font-semibold text-gray-900">Prezzo</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($batch->bookReservations as $index => $reservation)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $index + 1 }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900 font-medium">
                            {{ $reservation->bookListing->book->title }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600 font-mono">
                            {{ $reservation->bookListing->seller->code ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600 font-mono">
                            {{ $reservation->bookListing->book->isbn }}
                        </td>
                        <td class="px-6 py-4 text-sm">
                            <span class="px-3 py-1 rounded-full text-xs font-semibold
                                @switch($reservation->bookListing->condition)
                                    @case('like-new')
                                        bg-green-100 text-green-800
                                        @break
                                    @case('good')
                                        bg-blue-100 text-blue-800
                                        @break
                                    @case('fair')
                                        bg-yellow-100 text-yellow-800
                                        @break
                                    @case('poor')
                                        bg-red-100 text-red-800
                                        @break
                                @endswitch
                            ">
                                @switch($reservation->bookListing->condition)
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
                                @endswitch
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm font-semibold text-gray-900 text-right">
                            €{{ number_format($reservation->bookListing->price, 2) }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                            Nessun libro in questa prenotazione
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
