{{--
    COMPOSANT: KPI Card Uniforme
    Usage: @include('components.kpi-card', [
        'label' => 'Label du KPI',
        'value' => '45',
        'suffix' => 'F', // optionnel
        'icon' => 'building',
        'color' => 'blue', // blue, green, red, gray, gradient
        'trend' => '+12%', // optionnel
        'trendUp' => true // optionnel
    ])
--}}

@props([
    'label',
    'value',
    'suffix' => '',
    'icon' => 'chart',
    'color' => 'gray',
    'trend' => null,
    'trendUp' => true,
    'subtext' => null
])

@php
    $colorClasses = [
        'blue' => [
            'bg' => 'bg-blue-50',
            'icon' => 'text-blue-500',
            'value' => 'text-blue-600',
            'border' => 'border-blue-100'
        ],
        'green' => [
            'bg' => 'bg-green-50',
            'icon' => 'text-green-500',
            'value' => 'text-green-600',
            'border' => 'border-green-100'
        ],
        'red' => [
            'bg' => 'bg-red-50',
            'icon' => 'text-red-500',
            'value' => 'text-red-600',
            'border' => 'border-red-100'
        ],
        'gray' => [
            'bg' => 'bg-gray-50',
            'icon' => 'text-gray-400',
            'value' => 'text-gray-900',
            'border' => 'border-gray-100'
        ],
        'gradient' => [
            'bg' => 'bg-gradient-to-br from-[#274256] to-[#1a2e3d]',
            'icon' => 'text-blue-200',
            'value' => 'text-white',
            'border' => 'border-transparent'
        ]
    ];
    $colors = $colorClasses[$color] ?? $colorClasses['gray'];
    $isGradient = $color === 'gradient';
@endphp

<div class="@if($isGradient) {{ $colors['bg'] }} text-white shadow-xl shadow-blue-900/20 @else bg-white border {{ $colors['border'] }} shadow-sm @endif ontario-card-lift p-6 rounded-2xl transition-all duration-300 group">
    <div class="flex items-center justify-between">
        <div class="flex-1 min-w-0">
            <p class="text-[10px] font-black @if($isGradient) text-blue-200 @else text-gray-400 @endif uppercase tracking-widest mb-2 truncate">
                {{ $label }}
            </p>
            <div class="flex items-baseline gap-1.5">
                <span class="text-2xl sm:text-3xl font-black {{ $colors['value'] }} tabular-nums">
                    {{ $value }}
                </span>
                @if($suffix)
                    <span class="text-sm font-bold @if($isGradient) text-blue-200 @else text-gray-400 @endif">{{ $suffix }}</span>
                @endif
            </div>

            @if($trend || $subtext)
            <div class="mt-2 flex items-center gap-1.5">
                @if($trend)
                    <span class="inline-flex items-center gap-0.5 text-xs font-bold @if($trendUp) text-green-500 @else text-red-500 @endif">
                        @if($trendUp)
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
                        @else
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>
                        @endif
                        {{ $trend }}
                    </span>
                @endif
                @if($subtext)
                    <span class="text-xs @if($isGradient) text-blue-300 @else text-gray-400 @endif font-medium">{{ $subtext }}</span>
                @endif
            </div>
            @endif
        </div>

        <div class="w-12 h-12 {{ $colors['bg'] }} rounded-xl flex items-center justify-center {{ $colors['icon'] }} shrink-0 group-hover:scale-110 transition-transform">
            @switch($icon)
                @case('building')
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    @break
                @case('users')
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    @break
                @case('user')
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    @break
                @case('home')
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    @break
                @case('money')
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    @break
                @case('check')
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    @break
                @case('warning')
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    @break
                @case('chart')
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    @break
                @case('document')
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    @break
                @case('plus')
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    @break
                @case('clock')
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    @break
                @default
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
            @endswitch
        </div>
    </div>
</div>
