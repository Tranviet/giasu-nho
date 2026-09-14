@props([
    'percentage' => 0,
    'size' => 64,
    'stroke' => 6,
    'color' => '#3B82F6', // Blue default
])

@php
    $radius = ($size - $stroke) / 2;
    $circumference = 2 * pi() * $radius;
    $offset = $circumference - ($percentage / 100) * $circumference;
@endphp

<div class="relative inline-flex items-center justify-center shrink-0" style="width: {{ $size }}px; height: {{ $size }}px;">
    <svg class="w-full h-full -rotate-90" viewBox="0 0 {{ $size }} {{ $size }}">
        <!-- Background circle -->
        <circle
            cx="{{ $size / 2 }}"
            cy="{{ $size / 2 }}"
            r="{{ $radius }}"
            fill="transparent"
            stroke="currentColor"
            stroke-width="{{ $stroke }}"
            class="text-slate-100"
        />
        <!-- Active progress stroke -->
        <circle
            cx="{{ $size / 2 }}"
            cy="{{ $size / 2 }}"
            r="{{ $radius }}"
            fill="transparent"
            stroke="{{ $color }}"
            stroke-width="{{ $stroke }}"
            stroke-dasharray="{{ $circumference }}"
            stroke-dashoffset="{{ $offset }}"
            stroke-linecap="round"
            class="transition-all duration-700 ease-out"
        />
    </svg>
    <div class="absolute inset-0 flex flex-col items-center justify-center text-center">
        <span class="text-xs sm:text-sm font-black text-slate-800 leading-none">
            {{ $percentage }}%
        </span>
    </div>
</div>
