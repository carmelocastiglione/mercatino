@props(['reclaim'])

<div class="grid grid-cols-3 gap-6">
    <!-- Card: ID, Compratore, Venditore -->
    <div class="col-span-2 bg-white rounded-lg shadow-sm border border-gray-200 p-8">
        <div class="space-y-6">
            <!-- ID Row -->
            <div class="grid grid-cols-2 gap-6">
                <div>
                    <p class="text-xs text-gray-500 font-semibold uppercase mb-1 text-center">ID Reso</p>
                    <div class="w-full bg-gradient-to-br from-red-50 to-red-100 border-2 border-red-300 rounded-lg p-4 text-center">
                        <p class="text-3xl font-bold text-red-600 tracking-widest">#{{ $reclaim->id }}</p>
                    </div>
                </div>
                <div>
                    <p class="text-xs text-gray-500 font-semibold uppercase mb-1 text-center">Codice Acquirente</p>
                    <div class="w-full bg-gradient-to-br from-red-50 to-red-100 border-2 border-red-300 rounded-lg p-4 text-center">
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
