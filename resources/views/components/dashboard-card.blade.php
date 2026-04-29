@props(['title', 'description', 'count', 'href', 'bgColor' => 'blue', 'label' => 'GESTIONE'])

@php
    $colorMap = [
        'blue' => 'bg-blue-600 text-blue-100 text-blue-200',
        'purple' => 'bg-purple-600 text-purple-100 text-purple-200',
        'green' => 'bg-green-600 text-green-100 text-green-200',
        'yellow' => 'bg-yellow-600 text-yellow-100 text-yellow-200',
        'red' => 'bg-red-600 text-red-100 text-red-200',
        'indigo' => 'bg-indigo-600 text-indigo-100 text-indigo-200',
    ];
    
    [$bgClass, $textLightClass, $numberLightClass] = explode(' ', $colorMap[$bgColor]);
@endphp

<a href="{{ $href }}" class="{{ $bgClass }} rounded-xl p-8 text-white hover:shadow-lg transition">
    <div class="flex items-start justify-between">
        <div>
            <p class="{{ $textLightClass }} text-sm font-medium mb-2">{{ $label }}</p>
            <h2 class="text-3xl font-bold mb-4">{{ $title }}</h2>
            <p class="{{ $textLightClass }} mb-6">{{ $description }}</p>
            <div class="flex items-center font-medium hover:underline">
                Accedi
                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </div>
        </div>
        <div class="{{ $numberLightClass }} text-5xl font-bold">{{ $count ?? '—' }}</div>
    </div>
</a>
