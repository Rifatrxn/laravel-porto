@php
    $firstYear = $experiences->min('start_date');
    $years = $firstYear ? max(1, (int) date('Y') - (int) substr($firstYear, 0, 4)) : null;

    $items = array_values(array_filter([
        $years ? ['value' => $years, 'suffix' => '+', 'label' => 'Tahun Pengalaman'] : null,
        ['value' => $projects->count(), 'suffix' => '', 'label' => 'Proyek Selesai'],
        ['value' => $skills->count(), 'suffix' => '', 'label' => 'Teknologi Dikuasai'],
        $testimonials->count() ? ['value' => $testimonials->count(), 'suffix' => '', 'label' => 'Testimoni Klien'] : null,
    ]));
@endphp

@if (count($items))
    <section class="pb-8">
        <div class="container-x">
            <div class="card p-6 sm:p-8 grid grid-cols-2 lg:grid-cols-4 gap-6" data-reveal>
                @foreach ($items as $item)
                    <div class="text-center">
                        <p class="text-3xl sm:text-4xl font-bold text-gradient">
                            <span data-count-to="{{ $item['value'] }}" data-count-suffix="{{ $item['suffix'] }}">0</span>
                        </p>
                        <p class="text-xs sm:text-sm mt-1.5" style="color:var(--muted)">{{ $item['label'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endif
