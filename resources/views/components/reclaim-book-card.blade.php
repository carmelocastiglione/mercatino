@props(['reclaim'])

<div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
    <div class="px-8 py-6 border-b border-gray-200 bg-gray-50">
        <h2 class="text-2xl font-bold text-gray-900">Dettagli Libro in Reso</h2>
    </div>

    <div class="p-8">
        <div class="grid grid-cols-4 gap-6">
            <div>
                <p class="text-xs text-gray-500 font-semibold uppercase mb-2">Titolo</p>
                <p class="text-sm font-medium text-gray-900">{{ $reclaim->bookListing->book->title }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500 font-semibold uppercase mb-2">Autore</p>
                <p class="text-sm font-medium text-gray-900">{{ $reclaim->bookListing->book->author ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500 font-semibold uppercase mb-2">ISBN</p>
                <p class="text-sm font-medium text-gray-900 font-mono">{{ $reclaim->bookListing->book->isbn ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500 font-semibold uppercase mb-2">Condizione</p>
                <span class="px-3 py-1 rounded-full text-xs font-semibold
                    @switch($reclaim->bookListing->condition)
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
                    @switch($reclaim->bookListing->condition)
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
            </div>
        </div>
    </div>
</div>
