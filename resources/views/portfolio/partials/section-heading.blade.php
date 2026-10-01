@php
    /** Judul section yang dipakai berulang. */
    $label = $label ?? null;
    $title = $title ?? '';
    $subtitle = $subtitle ?? null;
    $align = $align ?? 'center';
@endphp

<div class="mb-12 {{ $align === 'center' ? 'text-center mx-auto max-w-2xl' : '' }}"
     data-reveal="{{ $align === 'center' ? 'zoom' : 'left' }}">
    @if ($label)
        <span class="text-xs font-semibold tracking-[0.24em] uppercase" style="color:var(--accent)">
            {{ $label }}
        </span>
    @endif

    <h2 class="text-3xl sm:text-4xl lg:text-[2.6rem] font-bold tracking-tight mt-3">{{ $title }}</h2>

    <div class="section-line mt-5 {{ $align === 'center' ? 'mx-auto' : '' }}"></div>

    @if ($subtitle)
        <p class="mt-5 leading-relaxed" style="color:var(--muted)">{{ $subtitle }}</p>
    @endif
</div>
