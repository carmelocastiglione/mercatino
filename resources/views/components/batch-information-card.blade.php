@props(['batch'])

<div class="grid grid-cols-3 gap-6">
    <!-- Card: ID, Buyer Code, Dates -->
    <div class="col-span-2 bg-white rounded-lg shadow-sm border border-gray-200 p-8">
        <div class="space-y-6">
            <!-- ID and Buyer Code Row -->
            <div class="grid grid-cols-2 gap-6">
                <div>
                    <p class="text-xs text-gray-500 font-semibold uppercase mb-1 text-center">ID Batch Vendite</p>
                    <div class="w-full bg-gradient-to-br from-green-50 to-green-100 border-2 border-green-300 rounded-lg p-4 text-center">
                        <p class="text-3xl font-bold text-green-600 tracking-widest">#{{ $batch->id }}</p>
                    </div>
                </div>
                <div>
                    <p class="text-xs text-gray-500 font-semibold uppercase mb-1 text-center">Codice Acquirente</p>
                    <div class="w-full bg-gradient-to-br from-green-50 to-green-100 border-2 border-green-300 rounded-lg p-4 text-center">
                        <p class="text-3xl font-bold text-green-600 tracking-widest">{{ $batch->buyer?->code ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>

            <!-- Dates -->
            <div class="space-y-4 pt-4 border-t border-gray-200">
                <div class="text-center">
                    <p class="text-xs text-gray-500 font-semibold uppercase mb-1">Data Registrazione</p>
                    <p class="text-sm font-medium text-gray-900">{{ $batch->created_at->format('d/m/Y H:i') }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Card: Buyer and Staff Details -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-8">
        <div class="space-y-4 flex flex-col justify-center items-center h-full text-center">
            @if($batch->buyer)
                <div>
                    <p class="text-xs text-gray-500 font-semibold uppercase mb-1">Acquirente</p>
                    <p class="text-lg font-bold text-gray-900">{{ $batch->buyer->name }} {{ $batch->buyer->surname }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 font-semibold uppercase mb-1">Email Acquirente</p>
                    <p class="text-sm text-gray-700">{{ $batch->buyer->email }}</p>
                </div>
            @endif
            <div>
                <p class="text-xs text-gray-500 font-semibold uppercase mb-1">Registrato da</p>
                <p class="text-lg font-bold text-gray-900">{{ $batch->creator->name }} {{ $batch->creator->surname }}</p>
            </div>
        </div>
    </div>
</div>
