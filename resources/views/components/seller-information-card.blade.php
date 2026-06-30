@props(['acquisition'])

<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <!-- Card: ID, Code, Dates -->
    <div class="md:col-span-2 bg-white rounded-lg shadow-sm border border-gray-200 p-8">
        <div class="space-y-6">
            <!-- ID and Code Row -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="flex flex-col">
                    <p class="text-xs text-gray-500 font-semibold uppercase mb-1 text-center">Codice Transazione</p>
                    <div class="flex-1 w-full bg-white border-2 border-blue-300 rounded-lg p-4 text-center flex flex-col justify-center items-center">
                        @if($acquisition->ean13)
                            <div class="flex justify-center">
                                <?php
                                    $barcode = (new \Picqer\Barcode\Types\TypeEan13())->getBarcode($acquisition->ean13);
                                    $renderer = new \Picqer\Barcode\Renderers\SvgRenderer();
                                    echo $renderer->render($barcode);
                                ?>
                            </div>
                            <p class="text-sm font-mono font-bold text-gray-900 mt-2">{{ $acquisition->ean13 }}</p>
                        @endif
                    </div>
                </div>
                <div class="flex flex-col">
                    <p class="text-xs text-gray-500 font-semibold uppercase mb-1 text-center">Codice Venditore</p>
                    <div class="flex-1 w-full bg-gradient-to-br from-blue-50 to-blue-100 border-2 border-blue-300 rounded-lg p-4 text-center flex items-center justify-center">
                        <p class="text-3xl font-bold text-blue-600 tracking-widest">{{ $acquisition->seller->code }}</p>
                    </div>
                </div>
            </div>

            <!-- Dates -->
            <div class="space-y-4 pt-4 border-t border-gray-200">
                <div class="text-center">
                    <p class="text-xs text-gray-500 font-semibold uppercase mb-1">Data Acquisizione</p>
                    <p class="text-sm font-medium text-gray-900">{{ $acquisition->created_at->format('d/m/Y H:i') }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Card: Seller Details -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-8">
        <div class="space-y-4 flex flex-col justify-center items-center h-full text-center">
            <div>
                <p class="text-xs text-gray-500 font-semibold uppercase mb-1">Venditore</p>
                <p class="text-lg font-bold text-gray-900">{{ $acquisition->seller->name }} {{ $acquisition->seller->surname }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500 font-semibold uppercase mb-1">Email</p>
                <p class="text-sm text-gray-700">{{ $acquisition->seller->email }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500 font-semibold uppercase mb-1">Registrato da</p>
                <p class="text-sm font-medium text-gray-900">{{ $acquisition->staff->name }} {{ $acquisition->staff->surname }}</p>
            </div>
        </div>
    </div>
</div>
