@props(['reclaim'])

<div class="grid grid-cols-3 gap-6">
    <!-- Card: ID, Compratore, Venditore -->
    <div class="col-span-2 bg-white rounded-lg shadow-sm border border-gray-200 p-8">
        <div class="space-y-6">
            <!-- ID Row -->
            <div class="grid grid-cols-2 gap-6">
                <div class="flex flex-col">
                    <p class="text-xs text-gray-500 font-semibold uppercase mb-1 text-center">Codice Transazione</p>
                    <div class="flex-1 w-full bg-white border-2 border-red-300 rounded-lg p-4 text-center flex flex-col justify-center items-center">
                        @if($reclaim->ean13)
                            <div class="flex justify-center">
                                <?php
                                    $barcode = (new \Picqer\Barcode\Types\TypeEan13())->getBarcode($reclaim->ean13);
                                    $renderer = new \Picqer\Barcode\Renderers\SvgRenderer();
                                    echo $renderer->render($barcode);
                                ?>
                            </div>
                            <p class="text-sm font-mono font-bold text-gray-900 mt-2">{{ $reclaim->ean13 }}</p>
                        @endif
                    </div>
                </div>
                <div class="flex flex-col">
                    <p class="text-xs text-gray-500 font-semibold uppercase mb-1 text-center">Codice Acquirente</p>
                    <div class="flex-1 w-full bg-gradient-to-br from-red-50 to-red-100 border-2 border-red-300 rounded-lg p-4 text-center flex items-center justify-center">
                        <p class="text-3xl font-bold text-red-600 tracking-widest">{{ $reclaim->buyer?->code ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>

            <!-- Dates -->
            <div class="space-y-4 pt-4 border-t border-gray-200">
                <div class="text-center">
                    <p class="text-xs text-gray-500 font-semibold uppercase mb-1">Data Reso</p>
                    <p class="text-sm font-medium text-gray-900">{{ $reclaim->created_at->format('d/m/Y H:i') }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Card: Compratore Details -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-8">
        <div class="space-y-4 flex flex-col justify-center items-center h-full text-center">
            <div>
                <p class="text-xs text-gray-500 font-semibold uppercase mb-1">Compratore</p>
                <p class="text-lg font-bold text-gray-900">{{ $reclaim->buyer->name }} {{ $reclaim->buyer->surname }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500 font-semibold uppercase mb-1">Email</p>
                <p class="text-sm text-gray-700">{{ $reclaim->buyer->email }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500 font-semibold uppercase mb-1">Codice Venditore</p>
                <p class="text-lg font-bold text-gray-900">{{ $reclaim->user->code ?? 'N/A' }}</p>
            </div>
        </div>
    </div>
</div>
