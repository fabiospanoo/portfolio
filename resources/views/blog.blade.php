<x-layout title="Blog | Fabio Spanò" navbarBrand="fab's blog" metaDescription="Articles and notes on web development by Fabio Spanò." metaType="blog">
    <div id="site-bg" aria-hidden="true"></div>

    <div class="body-style">
        <main class="blog-section">
            <ul class="blog-list">
                @forelse ($posts as $post)
                    <li class="blog-item" id="post-{{ $post->id }}" data-aos="fade-up" data-aos-delay="{{ min($loop->index * 60, 240) }}">
                        <div class="blog-item-content">
                            <p class="blog-date"><span class="text-comment">./</span> {{ $post->created_at->format('d F Y') }}</p>
                            <h2 class="blog-title">
                                <a class="bolder" href="{{ route('blog.show', $post) }}">{{ \Illuminate\Support\Str::limit($post->title, 45, '...') }}</a>
                            </h2>
                            <p class="blog-excerpt">{{ \Illuminate\Support\Str::limit($post->content, 65, '...') }}</p>
                        </div>
                        <a class="blog-read-more d-inline-flex align-items-center gap-2" href="{{ route('blog.show', $post) }}" aria-label="Read {{ $post->title }}">
                            <span>Read post</span> <i class="bi bi-arrow-right icon-arrow-right" aria-hidden="true"></i>
                        </a>
                    </li>
                @empty
                    <li class="blog-item">
                        <p class="contact-prompt">No posts yet.</p>
                    </li>
                @endforelse
            </ul>
        </main>
    </div>
</x-layout>