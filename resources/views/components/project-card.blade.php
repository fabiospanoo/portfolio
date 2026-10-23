@php
    $button = $buttonClass();
    $aosDelay = $index < 3 ? (650 + $index * 150) : (($index % 3) * 100);
    $aosOffset = $index < 3 ? 0 : 30;
@endphp

<div class="col-12 col-md-6 col-xl-4 project-col" data-index="{{ $index }}" data-aos="fade-up" data-aos-delay="{{ $aosDelay }}" data-aos-offset="{{ $aosOffset }}">
    <div class="card card-proj text-bg-dark" style="width: 18rem;">
        <div class="card-body">
            <h5 class="card-title bold" style="padding-bottom: 8px;">{{ $project->title }}</h5>
            <img src="{{ $project->image ?: 'https://picsum.photos/1600/900' }}" class="card-img-top" alt="..." style="border-radius: 0px; padding: 1rem; padding-bottom: 1rem; padding-left: 0px; padding-right: 0px; border-top: 1px solid #343434; border-bottom: 1px solid #343434;">
            <p class="card-text pt-2 project-card-text">{{ $project->text }}</p>
            <div class="mt-auto">
                @if ($project->tags)
                    <div class="project-tags">
                        @foreach ($project->tags as $tag)
                            <span class="project-tag {{ $tagClass($tag) }}">{{ $tag }}</span>
                        @endforeach
                    </div>
                @endif
                @if ($project->url)
                    <a href="{{ $project->url }}" target="_blank" rel="noopener" class="btn btn-{{ $button }} align-self-start d-inline-flex align-items-center gap-1">Github <i class="bi bi-arrow-up-right btn-external-icon"></i></a>
                @endif
            </div>
        </div>
    </div>
</div>