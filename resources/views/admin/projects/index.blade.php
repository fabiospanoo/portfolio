<x-admin-layout title="projects // admin">
    <div class="admin-head">
        <div>
            <p class="admin-eyebrow">./ projects</p>
            <h1 class="admin-title">All projects</h1>
        </div>
        <a href="{{ route('admin.projects.create') }}" class="btn btn-senary">+ New project</a>
    </div>

    @if ($projects->isEmpty())
        <div class="admin-card admin-empty">
            <span class="text-comment">&gt;</span> no projects yet. <a class="text-link-custom" href="{{ route('admin.projects.create') }}">create the first one</a>.
        </div>
    @else
        <div class="admin-table">
            <div class="admin-table-head">
                <span>title</span>
                <span>tags</span>
                <span>repo</span>
                <span class="text-end">actions</span>
            </div>

            @foreach ($projects as $project)
                <div class="admin-table-row">
                    <div class="admin-cell-title">
                        <span class="text-comment">./ </span>{{ $project->title }}
                    </div>

                    <div class="admin-cell-tags">
                        @forelse ($project->tags ?? [] as $tag)
                            <span class="project-tag {{ \App\View\Components\ProjectCard::resolveTagClass($tag) }}">{{ $tag }}</span>
                        @empty
                            <span class="admin-hint">—</span>
                        @endforelse
                    </div>

                    <div class="admin-cell-repo">
                        @if ($project->url)
                            <a class="text-link-custom" href="{{ $project->url }}" target="_blank" rel="noopener">link</a>
                        @else
                            <span class="admin-hint">—</span>
                        @endif
                    </div>

                    <div class="admin-cell-actions">
                        <a href="{{ route('admin.projects.edit', $project) }}" class="btn btn-quaternary btn-sm">Edit</a>

                        <form action="{{ route('admin.projects.destroy', $project) }}" method="POST"
                            onsubmit="return confirm('Delete “{{ $project->title }}”?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-primary btn-sm">Delete</button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</x-admin-layout>
