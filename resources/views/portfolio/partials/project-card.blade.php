@php
    /** @var \App\Models\Project $project */
    $thumb = media_url($project->thumbnail);
    $techs = $project->tech_stack ?? [];
@endphp

<article class="card shine overflow-hidden group tilt h-full flex flex-col"
         data-tilt="7"
         data-reveal="zoom"
         data-project-category="{{ $project->category ?: 'lainnya' }}"
         data-filter-item
         data-filter-value="{{ Str::slug($project->category ?: 'lainnya') }}"
         style="--reveal-delay:{{ $delay ?? 0 }}ms">

    <a href="{{ route('portfolio.project', $project) }}" class="block relative overflow-hidden aspect-[16/10]">
        @if ($thumb)
            <img src="{{ $thumb }}" alt="{{ $project->title }}"
                 loading="lazy"
                 class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
        @else
            <div class="w-full h-full grid place-items-center
                        bg-[linear-gradient(140deg,color-mix(in_srgb,var(--accent)_28%,transparent),color-mix(in_srgb,var(--accent-2)_28%,transparent))]">
                <span class="text-5xl opacity-80">📁</span>
            </div>
        @endif

        <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/10 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>

        @if ($project->is_featured)
            <span class="absolute top-3 left-3 badge badge-on">⭐ Unggulan</span>
        @endif

        @if ($project->category)
            <span class="absolute top-3 right-3 chip">{{ $project->category }}</span>
        @endif

        <span class="absolute bottom-3 right-3 w-9 h-9 rounded-full grid place-items-center text-white
                     bg-[linear-gradient(120deg,var(--accent),var(--accent-2))]
                     translate-y-3 opacity-0 group-hover:translate-y-0 group-hover:opacity-100 transition-all duration-500">
            →
        </span>
    </a>

    <div class="p-5 flex flex-col flex-1 tilt-inner">
        <div class="flex items-start justify-between gap-3">
            <h3 class="font-semibold text-lg leading-snug">
                <a href="{{ route('portfolio.project', $project) }}" class="hover:text-[var(--accent)] transition-colors">
                    {{ $project->title }}
                </a>
            </h3>
        </div>

        @if ($project->completed_at)
            <p class="text-xs mt-1" style="color:var(--muted)">{{ indonesian_month($project->completed_at) }}</p>
        @endif

        @if ($project->short_description)
            <p class="mt-3 text-sm leading-relaxed line-clamp-3" style="color:var(--muted)">
                {{ $project->short_description }}
            </p>
        @endif

        @if (!empty($techs))
            <div class="mt-4 flex flex-wrap gap-1.5">
                @foreach (array_slice($techs, 0, 4) as $tech)
                    <span class="text-[11px] px-2 py-1 rounded-md"
                          style="background:var(--surface);border:1px solid var(--border);color:var(--muted)">{{ $tech }}</span>
                @endforeach
                @if (count($techs) > 4)
                    <span class="text-[11px] px-2 py-1 rounded-md" style="color:var(--muted)">+{{ count($techs) - 4 }}</span>
                @endif
            </div>
        @endif

        <div class="mt-auto pt-5 flex items-center gap-3 text-xs">
            @if ($project->demo_url)
                <a href="{{ $project->demo_url }}" target="_blank" rel="noopener"
                   class="flex items-center gap-1.5 hover:text-[var(--accent)] transition-colors font-medium">
                    🔗 Demo
                </a>
            @endif
            @if ($project->repo_url)
                <a href="{{ $project->repo_url }}" target="_blank" rel="noopener"
                   class="flex items-center gap-1.5 hover:text-[var(--accent)] transition-colors font-medium">
                    💻 Kode
                </a>
            @endif
            <a href="{{ route('portfolio.project', $project) }}"
               class="ml-auto flex items-center gap-1.5 font-medium hover:text-[var(--accent)] transition-colors">
                Detail →
            </a>
        </div>
    </div>
</article>
