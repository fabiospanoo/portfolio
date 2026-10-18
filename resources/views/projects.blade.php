<x-layout title="Fabio Spanò // Agentic AI, Java, Web Developer" navbarBrand="fabio spanò" metaDescription="Portfolio of Fabio Spanò, web developer and agentic AI specialist. Projects built with Java, Laravel, Blade and more." :projects="$projects">
    <div class="body-style">
        <header>
            <div class="header-custom">
                <h1 class="title h2 bolder mt-5 pt-5" data-aos="fade-up">Web developer | Agentic AI | Java specialist</h1>
                <div class="container-introduction" data-aos="fade-up" data-aos-delay="200">
                    <p class="contact-prompt">I specialize in Java and Agentic AI while completing my web development master's at AuLab. My technical interests extend to Raspbian-based projects and building custom network environments.</p>
                    <p class="contact-prompt">Interested in my computational knowledge of AI agents and Java? Let's talk.</p>
                    <p class="contact-links">
                        <span class="text-keyword">email</span> = <a class="text-link-custom copy-email" href="copy:fabiospanoo@outlook.it" title="Click to copy">'fabiospanoo [at] outlook (dot) it' ;</a>
                    </p>
                </div>
            </div>
            <section class="section-BlogCard">
                @if ($latestPost)
                    <div class="card card-proj text-bg-dark" style="width: 18rem;" data-aos="fade-up" data-aos-delay="400">
                        <div class="card-body blog-card-body">
                            <h6 class="card-subtitle subtitle mb-3 text-uppercase"><span class="text-comment">./</span> Latest from the blog</h6>
                            <h5 class="card-title bold mb-2"><a href="{{ route('blog.show', $latestPost) }}">{{ \Illuminate\Support\Str::limit($latestPost->title, 45, '...') }}</a></h5>
                            <p class="card-text blog-card-preview">{{ \Illuminate\Support\Str::limit($latestPost->content, 65, '...') }}</p>
                            <a href="{{ route('blog.show', $latestPost) }}" class="card-link d-inline-flex align-items-center gap-2"><span>Read post</span> <i class="bi bi-arrow-right icon-arrow-right"></i></a>
                        </div>
                    </div>
                @endif
                <div class="row g-3 projects-grid" id="projects-grid" style="padding-top: 28px;">
                    @foreach ($projects as $project)
                        <x-project-card :project="$project" :index="$loop->index" />
                    @endforeach
                </div>
                <script>
                    (function () {
                        var grid = document.getElementById('projects-grid');
                        if (!grid) return;
                        var w = window.innerWidth;
                        var sw = window.screen.availWidth || window.screen.width || w;
                        var r = w / sw;
                        var cols = (w < 768) ? 1 : ((r <= 0.68 || w < 1200) ? 2 : 3);
                        grid.classList.add('grid-' + cols + '-col');
                    })();
                </script>
            </section>
        </header>
    </div>
</x-layout>