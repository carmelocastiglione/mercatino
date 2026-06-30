@props(['batch'])

<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <!-- Card: ID, Code, Dates -->
    <div class="md:col-span-2 bg-white rounded-lg shadow-sm border border-gray-200 p-8">
        <div class="space-y-6">
            <!-- ID and Code Row -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="flex flex-col">
                    <p class="text-xs text-gray-500 font-semibold uppercase mb-1 text-center">Codice Prenotazione</p>
                    <div class="flex-1 w-full bg-white border-2 border-blue-300 rounded-lg p-4 text-center flex flex-col justify-center items-center">
                        @if($batch->ean13)
                            <div class="flex justify-center">
                                <?php
                                    $barcode = (new \Picqer\Barcode\Types\TypeEan13())->getBarcode($batch->ean13);
                                    $renderer = new \Picqer\Barcode\Renderers\SvgRenderer();
                                    echo $renderer->render($barcode);
                                ?>
                            </div>
                            <p class="text-sm font-mono font-bold text-gray-900 mt-2">{{ $batch->ean13 }}</p>
                        @endif
                    </div>
                </div>
                <div class="flex flex-col">
                    <p class="text-xs text-gray-500 font-semibold uppercase mb-1 text-center">Codice Studente</p>
                    <div class="flex-1 w-full bg-gradient-to-br from-blue-50 to-blue-100 border-2 border-blue-300 rounded-lg p-4 text-center flex items-center justify-center">
                        <p class="text-3xl font-bold text-blue-600 tracking-widest">{{ $batch->user->code }}</p>
                    </div>
                </div>
            </div>

            <!-- Dates -->
            <div class="space-y-4 pt-4 border-t border-gray-200">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-center">
                    <div>
                        <p class="text-xs text-gray-500 font-semibold uppercase mb-1">Data Prenotazione</p>
                        <p class="text-sm font-medium text-gray-900">{{ $batch->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                    @if($batch->scheduledDeliveryDate)
                        <div>
                            <p class="text-xs text-gray-500 font-semibold uppercase mb-1">Data Consegna</p>
                            <p class="text-sm font-medium text-gray-900">{{ $batch->scheduledDeliveryDate->scheduled_date->format('d/m/Y') }}</p>
                        </div>
                    @else
                        <div></div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Card: Student Details -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-8">
        <div class="space-y-4 flex flex-col justify-center items-center h-full text-center">
            <div>
                <p class="text-xs text-gray-500 font-semibold uppercase mb-1">Studente</p>
                <p class="text-lg font-bold text-gray-900">{{ $batch->user->name }} {{ $batch->user->surname }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500 font-semibold uppercase mb-1">Email</p>
                <p class="text-sm text-gray-700">{{ $batch->user->email }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500 font-semibold uppercase mb-1">Scuola</p>
                <p class="text-sm font-medium text-gray-900">{{ $batch->school->name }}</p>
            </div>
            @if($batch->notes)
                <div class="pt-3 border-t border-gray-200 w-full">
                    <p class="text-xs text-gray-500 font-semibold uppercase mb-1">Note</p>
                    <p class="text-sm text-gray-700">{{ $batch->notes }}</p>
                </div>
            @endif
        </div>
    </div>
</div>
