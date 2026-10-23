@php
    $editing = $project->exists;
    $action = $editing ? route('admin.projects.update', $project) : route('admin.projects.store');
    $selectedTags = (array) old('tags', $project->tags ?? []);
@endphp

<x-admin-layout :title="($editing ? 'edit' : 'new') . ' project // admin'">
    <div class="admin-head">
        <div>
            <p class="admin-eyebrow">./ projects {{ $editing ? '/ edit' : '/ new' }}</p>
            <h1 class="admin-title">{{ $editing ? 'Edit project' : 'New project' }}</h1>
        </div>
        <a href="{{ route('admin.projects.index') }}" class="admin-back">&larr; back to list</a>
    </div>

    <div class="admin-card">
        <form action="{{ $action }}" method="POST" class="admin-form">
            @csrf
            @if ($editing)
                @method('PUT')
            @endif

            <div class="mb-3">
                <label for="title" class="form-label"><span class="text-comment">./ </span>title</label>
                <input type="text" class="form-control" id="title" name="title" value="{{ old('title', $project->title) }}" required>
                @error('title') <div class="admin-error">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label for="text" class="form-label"><span class="text-comment">./ </span>description</label>
                <textarea class="form-control" id="text" name="text" rows="4" required>{{ old('text', $project->text) }}</textarea>
                @error('text') <div class="admin-error">{{ $message }}</div> @enderror
            </div>

            <div class="row g-3">
                <div class="col-12 col-md-6">
                    <label for="url" class="form-label"><span class="text-comment">./ </span>repository url</label>
                    <input type="url" class="form-control" id="url" name="url" value="{{ old('url', $project->url) }}" placeholder="https://github.com/...">
                    @error('url') <div class="admin-error">{{ $message }}</div> @enderror
                </div>

                <div class="col-12 col-md-6">
                    <label for="image" class="form-label"><span class="text-comment">./ </span>image url</label>
                    <input type="url" class="form-control" id="image" name="image" value="{{ old('image', $project->image) }}" placeholder="https://...">
                    @error('image') <div class="admin-error">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="mb-3 mt-3">
                <label class="form-label"><span class="text-comment">./ </span>tags</label>
                <div class="admin-tag-picker">
                    @foreach ($tags as $tag)
                        <label class="admin-tag-chip">
                            <input type="checkbox" name="tags[]" value="{{ $tag }}" @checked(in_array($tag, $selectedTags, true))>
                            <span class="project-tag {{ \App\View\Components\ProjectCard::resolveTagClass($tag) }}">{{ $tag }}</span>
                        </label>
                    @endforeach
                </div>
                @error('tags') <div class="admin-error">{{ $message }}</div> @enderror
            </div>

            <div class="admin-actions">
                <a href="{{ route('admin.projects.index') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-quaternary">{{ $editing ? 'Save changes' : 'Create project' }}</button>
            </div>
        </form>
    </div>
</x-admin-layout>
