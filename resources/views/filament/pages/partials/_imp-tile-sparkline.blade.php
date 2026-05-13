@props(['points' => [], 'color' => '#2563eb'])
@php
    $path = \App\Support\SparklinePath::build($points, 100, 30);
@endphp
@if ($path)
    <svg class="imp-tile-spark" viewBox="0 0 100 30" preserveAspectRatio="none" aria-hidden="true">
        <path d="{{ $path }}" fill="none" stroke="{{ $color }}" stroke-width="1.5"
              stroke-linecap="round" stroke-linejoin="round" opacity="0.22" />
    </svg>
@endif
