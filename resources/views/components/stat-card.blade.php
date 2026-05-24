@props([
    'title' => '',
    'amount' => 0,
    'bgColor' => 'blue',
    'accentColor' => 'blue',
])

@php
    $bgClasses = match($bgColor) {
        'green' => 'bg-gradient-to-br from-green-50 to-green-100',
        'blue' => 'bg-gradient-to-br from-blue-50 to-blue-100',
        'purple' => 'bg-gradient-to-br from-purple-50 to-purple-100',
        'orange' => 'bg-gradient-to-br from-orange-50 to-orange-100',
        default => 'bg-gradient-to-br from-gray-50 to-gray-100',
    };
    
    $accentClasses = match($accentColor) {
        'green' => 'text-green-600',
        'blue' => 'text-blue-600',
        'purple' => 'text-purple-600',
        'orange' => 'text-orange-600',
        default => 'text-gray-600',
    };
@endphp

<div class="{{ $bgClasses }} rounded-2xl p-8 shadow-sm border-2 {{ match($accentColor) {
    'green' => 'border-green-200',
    'blue' => 'border-blue-200',
    'purple' => 'border-purple-200',
    'orange' => 'border-orange-200',
    default => 'border-gray-200',
} }} hover:shadow-md transition-shadow duration-300">
    <div class="flex items-start justify-between">
        <div class="flex-1">
            <p class="text-gray-600 font-semibold text-sm tracking-wide uppercase">{{ $title }}</p>
            <p class="mt-3 text-5xl font-black {{ $accentClasses }}">
                {{ number_format($amount, 2, ',', '.') }}€
            </p>
        </div>
    </div>
</div>
