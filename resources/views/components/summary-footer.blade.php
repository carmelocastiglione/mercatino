@props(['batch' => null, 'acquisition' => null])

@php
    if ($acquisition) {
        $count = $acquisition->bookListings->count();
        $total = $acquisition->total_price;
        $label = 'Totale Acquisizione';
    } else {
        $count = $batch->deliveries()->count();
        $total = $batch->deliveries()->sum('price');
        $label = 'Totale Prenotazione';
    }
@endphp

@if ($acquisition)
    <div class="bg-gradient-to-r from-blue-50 to-blue-100 border-2 border-blue-300 rounded-lg p-8">
        <div class="flex justify-between items-center">
            <div>
                <p class="text-sm text-gray-600 mb-2">Numero Libri</p>
                <p class="text-4xl font-bold text-gray-900">{{ $count }}</p>
            </div>
            <div class="text-right">
                <p class="text-sm text-gray-600 mb-2">{{ $label }}</p>
                <p class="text-4xl font-bold text-blue-600">€{{ number_format($total, 2) }}</p>
            </div>
        </div>
    </div>
@else
    <div class="bg-gradient-to-r from-green-50 to-green-100 border-2 border-green-300 rounded-lg p-8">
        <div class="flex justify-between items-center">
            <div>
                <p class="text-sm text-gray-600 mb-2">Numero Libri</p>
                <p class="text-4xl font-bold text-gray-900">{{ $count }}</p>
            </div>
            <div class="text-right">
                <p class="text-sm text-gray-600 mb-2">{{ $label }}</p>
                <p class="text-4xl font-bold text-green-600">€{{ number_format($total, 2) }}</p>
            </div>
        </div>
    </div>
@endif
