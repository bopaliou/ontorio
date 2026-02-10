{{--
    COMPOSANT: KPI Card Modern Widget
    Usage: @include('components.kpi-card', [
        'label' => 'Label du KPI',
        'value' => '45',
        'suffix' => 'F', // optionnel
        'icon' => 'building',
        'color' => 'blue', // blue, green, red, gray, gradient
        'trend' => '+12%', // optionnel
        'trendUp' => true, // optionnel
        'subtext' => 'vs N-1' // optionnel
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
    $colorConfig = [
        'blue' => [
            'text' => 'text-blue-600',
            'bg_light' => 'bg-blue-50',
            'border' => 'border-blue-100',
            'icon' => 'text-blue-600',
            'gradient' => 'from-blue-500 to-blue-600',
            'pill_text' => 'text-blue-700',
            'pill_bg' => 'bg-blue-100',
        ],
        'green' => [
            'text' => 'text-emerald-600',
            'bg_light' => 'bg-emerald-50',
            'border' => 'border-emerald-100',
            'icon' => 'text-emerald-600',
            'gradient' => 'from-emerald-500 to-emerald-600',
            'pill_text' => 'text-emerald-700',
            'pill_bg' => 'bg-emerald-100',
        ],
        'red' => [
            'text' => 'text-[#cb2d2d]',
            'bg_light' => 'bg-red-50',
            'border' => 'border-red-100',
            'icon' => 'text-[#cb2d2d]',
            'gradient' => 'from-[#cb2d2d] to-[#a01a1a]',
            'pill_text' => 'text-[#cb2d2d]',
            'pill_bg' => 'bg-red-50',
        ],
        'gray' => [
            'text' => 'text-gray-900',
            'bg_light' => 'bg-gray-50',
            'border' => 'border-gray-200',
            'icon' => 'text-gray-400',
            'gradient' => 'from-gray-500 to-gray-600',
            'pill_text' => 'text-gray-700',
            'pill_bg' => 'bg-gray-100',
        ],
        'gradient' => [ // Fallback logic for gradient, though less used in this new style
            'text' => 'text-white',
            'bg_light' => 'bg-[#274256]',
            'border' => 'border-transparent',
            'icon' => 'text-white/20',
            'gradient' => 'from-[#274256] to-[#1a2e3d]',
            'pill_text' => 'text-white',
            'pill_bg' => 'bg-white/10',
        ]
    ];

    $theme = $colorConfig[$color] ?? $colorConfig['gray'];
    $isGradient = $color === 'gradient';
@endphp

<div class="relative overflow-hidden rounded-3xl p-6 transition-all duration-300 group hover:-translate-y-1 hover:shadow-xl
    @if($isGradient)
        bg-gradient-to-br {{ $theme['gradient'] }} text-white shadow-lg shadow-blue-900/20
    @else
        bg-white border border-gray-100 shadow-sm
    @endif
">

    {{-- Decorative Color Strip (Top) --}}
    @if(!$isGradient)
        <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r {{ $theme['gradient'] }} opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
    @endif

    {{-- Watermark Icon (Background) --}}
    <div class="absolute -bottom-6 -right-6 opacity-[0.08] transform rotate-[-15deg] group-hover:scale-110 group-hover:rotate-0 transition-all duration-500">
        <div class="@if($isGradient) text-white @else {{ $theme['icon'] }} @endif w-32 h-32">
             @switch($icon)
                @case('building') <svg class="w-full h-full" fill="currentColor" viewBox="0 0 24 24"><path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg> @break
                @case('users') <svg class="w-full h-full" fill="currentColor" viewBox="0 0 24 24"><path d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg> @break
                @case('user') <svg class="w-full h-full" fill="currentColor" viewBox="0 0 24 24"><path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg> @break
                @case('money') <svg class="w-full h-full" fill="currentColor" viewBox="0 0 24 24"><path d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg> @break
                @case('document') <svg class="w-full h-full" fill="currentColor" viewBox="0 0 24 24"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg> @break
                @case('clock') <svg class="w-full h-full" fill="currentColor" viewBox="0 0 24 24"><path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg> @break
                @default <svg class="w-full h-full" fill="currentColor" viewBox="0 0 24 24"><path d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
            @endswitch
        </div>
    </div>

    {{-- Content --}}
    <div class="relative z-10 flex flex-col h-full justify-between">
        {{-- Header: Label + Trend --}}
        <div class="flex items-start justify-between mb-4">
            <h3 class="text-[11px] font-bold uppercase tracking-[0.2em] @if($isGradient) text-blue-100 @else text-gray-400 @endif">{{ $label }}</h3>

            @if($trend)
                <div class="flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold border
                    @if($isGradient) bg-white/10 border-white/10 text-white
                    @elseif($trendUp) bg-emerald-50 border-emerald-100 text-emerald-600
                    @else bg-red-50 border-red-100 text-red-600 @endif">

                    @if($trendUp)
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 10l7-7m0 0l7 7"/></svg>
                    @else
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 14l-7 7m0 0l-7-7"/></svg>
                    @endif
                    {{ $trend }}
                </div>
            @endif
        </div>

        {{-- Main Value --}}
        <div>
            <div class="flex items-baseline gap-1">
                <span class="text-4xl font-black tracking-tighter @if($isGradient) text-white @else text-gray-900 @endif tabular-nums">
                    {{ $value }}
                </span>
                @if($suffix)
                    <span class="text-lg font-bold @if($isGradient) text-blue-200 @else text-gray-400 @endif">{{ $suffix }}</span>
                @endif
            </div>

            @if($subtext)
                <p class="mt-1 text-xs font-medium @if($isGradient) text-blue-200 @else text-gray-400 @endif">{{ $subtext }}</p>
            @endif
        </div>
    </div>
</div>
