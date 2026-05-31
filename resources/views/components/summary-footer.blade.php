@props(['batch'])

<div class="bg-gradient-to-r from-green-50 to-green-100 border-2 border-green-300 rounded-lg p-8">
    <div class="flex justify-between items-center">
        <div>
            <p class="text-sm text-gray-600 mb-2">Numero Libri</p>
            <p class="text-4xl font-bold text-gray-900">{{ $batch->deliveries()->count() }}</p>
        </div>
        <div class="text-right">
            <p class="text-sm text-gray-600 mb-2">Totale Prenotazione</p>
            <p class="text-4xl font-bold text-green-600">€{{ number_format($batch->deliveries()->sum('price'), 2) }}</p>
        </div>
    </div>
</div>
