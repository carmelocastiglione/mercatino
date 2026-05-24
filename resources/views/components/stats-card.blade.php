@props([
    'label' => '',
    'value' => '',
    'color' => 'blue',
    'formatted' => false,
])

@php
    $colorClasses = match($color) {
        'green' => 'from-green-50 to-green-100 border-green-200 text-green-600',
        'blue' => 'from-blue-50 to-blue-100 border-blue-200 text-blue-600',
        'red' => 'from-red-50 to-red-100 border-red-200 text-red-600',
        'yellow' => 'from-yellow-50 to-yellow-100 border-yellow-200 text-yellow-600',
        'purple' => 'from-purple-50 to-purple-100 border-purple-200 text-purple-600',
        'indigo' => 'from-indigo-50 to-indigo-100 border-indigo-200 text-indigo-600',
        'orange' => 'from-orange-50 to-orange-100 border-orange-200 text-orange-600',
        default => 'from-blue-50 to-blue-100 border-blue-200 text-blue-600',
    };
@endphp

<div class="bg-gradient-to-br {{ $colorClasses }} rounded-2xl p-8 shadow-sm border-2">
    <p class="text-gray-600 font-semibold text-sm tracking-wide uppercase">{{ $label }}</p>
    <p class="mt-3 text-5xl font-black {{ 'text-' . $color . '-600' }}">
        @if($formatted)
            {{ number_format($value, 2, ',', '.') }}€
        @else
            {{ $value }}
        @endif
    </p>
</div>
