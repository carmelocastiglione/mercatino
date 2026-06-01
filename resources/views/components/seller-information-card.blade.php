@props(['acquisition'])

<div class="grid grid-cols-3 gap-6">
    <!-- Card: ID, Code, Dates -->
    <div class="col-span-2 bg-white rounded-lg shadow-sm border border-gray-200 p-8">
        <div class="space-y-6">
            <!-- ID and Code Row -->
            <div class="grid grid-cols-2 gap-6">
                <div>
                    <p class="text-xs text-gray-500 font-semibold uppercase mb-1 text-center">ID Acquisizione</p>
                    <div class="w-full bg-gradient-to-br from-blue-50 to-blue-100 border-2 border-blue-300 rounded-lg p-4 text-center">
                        <p class="text-3xl font-bold text-blue-600 tracking-widest">#{{ $acquisition->id }}</p>
                    </div>
                </div>
                <div>
                    <p class="text-xs text-gray-500 font-semibold uppercase mb-1 text-center">Codice Venditore</p>
                    <div class="w-full bg-gradient-to-br from-blue-50 to-blue-100 border-2 border-blue-300 rounded-lg p-4 text-center">
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
