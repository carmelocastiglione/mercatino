@props([
    'type' => 'info', // info, warning, success, error
    'title' => null,
    'message' => '',
    'icon' => null,
    'class' => '',
])

@php
    $types = [
        'info' => [
            'bg' => 'bg-gradient-to-r from-blue-50 to-indigo-50',
            'border' => 'border-blue-300',
            'text-title' => 'text-blue-900',
            'text-message' => 'text-blue-800',
            'icon-color' => 'text-blue-600',
            'default-icon' => 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
        ],
        'warning' => [
            'bg' => 'bg-gradient-to-r from-yellow-50 to-orange-50',
            'border' => 'border-yellow-300',
            'text-title' => 'text-yellow-900',
            'text-message' => 'text-yellow-800',
            'icon-color' => 'text-yellow-600',
            'default-icon' => 'M12 9v2m0 4v2m0-12a9 9 0 110 18 9 9 0 010-18zm0 2a7 7 0 100 14 7 7 0 000-14z',
        ],
        'success' => [
            'bg' => 'bg-gradient-to-r from-green-50 to-emerald-50',
            'border' => 'border-green-300',
            'text-title' => 'text-green-900',
            'text-message' => 'text-green-800',
            'icon-color' => 'text-green-600',
            'default-icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
        ],
        'error' => [
            'bg' => 'bg-gradient-to-r from-red-50 to-pink-50',
            'border' => 'border-red-300',
            'text-title' => 'text-red-900',
            'text-message' => 'text-red-800',
            'icon-color' => 'text-red-600',
            'default-icon' => 'M12 8v4m0 4v.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
        ],
    ];

    $style = $types[$type] ?? $types['info'];
    $iconPath = $icon ?? $style['default-icon'];
@endphp

<div class="mb-8 p-4 {{ $style['bg'] }} border {{ $style['border'] }} rounded-lg {{ $class }}">
    <div class="flex items-start">
        <div class="flex-shrink-0">
            <svg class="h-6 w-6 {{ $style['icon-color'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $iconPath }}"></path>
            </svg>
        </div>
        <div class="ml-3">
            @if($title)
                <h3 class="text-sm font-semibold {{ $style['text-title'] }} mb-1">{{ $title }}</h3>
            @endif
            <p class="text-sm {{ $style['text-message'] }}">
                {{ $message }}
            </p>
        </div>
    </div>
</div>
