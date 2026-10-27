<x-layout
    :title="$post->title.' // Fabio Spanò'"
    :meta-description="\Illuminate\Support\Str::limit($post->content, 160)"
    metaType="article"
    :post="$post">
    <div id="site-bg" aria-hidden="true"></div>

    <div class="body-style">
        <main class="blog-article">
            <a class="blog-back-link d-inline-flex align-items-center gap-2" href="{{ route('blog.index') }}" data-aos="fade-up"><i class="bi bi-arrow-left icon-arrow-left"></i> <span>Back to blog</span></a>

            <article data-aos="fade-up" data-aos-delay="100">
                <header class="blog-article-header">
                    <p class="blog-date"><span class="text-comment">./</span> {{ $post->created_at->format('d F Y') }}</p>
                    @if ($post->category)
                        <p class="blog-category"><span class="text-comment">#</span> {{ $post->category }}</p>
                    @endif
                    <h1 class="bolder">{{ $post->title }}</h1>
                </header>

                <div class="blog-article-content contact-prompt" style="margin-top: 48px;">
                    {!! nl2br(e($post->content)) !!}
                </div>
            </article>
        </main>
    </div>
</x-layout>