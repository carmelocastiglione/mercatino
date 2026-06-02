@props(['reclaim'])

<div class="bg-gradient-to-r from-red-50 to-red-100 border-2 border-red-300 rounded-lg p-8">
    <div class="flex justify-between items-center">
        <div>
            <p class="text-sm text-gray-600 mb-2">Status Reso</p>
            <p class="text-4xl font-bold text-gray-900">
                @if ($reclaim->status === 'approved')
                    <span class="text-green-600">✓ Approvato</span>
                @elseif ($reclaim->status === 'rejected')
                    <span class="text-red-600">✕ Rifiutato</span>
                @else
                    <span class="text-yellow-600">⏳ In Sospeso</span>
                @endif
            </p>
        </div>
        <div class="text-right">
            <p class="text-sm text-gray-600 mb-2">Importo da Restituire</p>
            <p class="text-4xl font-bold text-red-600">
                @if ($reclaim->status === 'rejected')
                    -
                @else
                    €{{ number_format($reclaim->bookListing->price_sell, 2) }}
                @endif
            </p>
        </div>
    </div>
</div>
