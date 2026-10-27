<x-layout title="Fabio Spanò // Agentic AI, Java, Web Developer" navbarBrand="fabio spanò" metaDescription="Portfolio of Fabio Spanò, web developer and agentic AI specialist. Projects built with Java, Laravel, Blade and more." :projects="$projects">
    <div class="body-style">
        <header>
            <div class="header-custom">
                <h1 class="title h2 bolder mt-5 pt-5" data-aos="fade-up">Web developer | Agentic AI | Java specialist</h1>
                <div class="container-introduction" data-aos="fade-up" data-aos-delay="200">
                    <p class="contact-prompt">I specialize in Java and Agentic AI while completing my web development master's at AuLab. My technical interests extend to Raspbian-based projects and building custom network environments.</p>
                    <p class="contact-prompt">Interested in my computational knowledge of AI agents and Java? Let's talk.</p>
                    <div class="contact-actions d-flex flex-wrap gap-3 align-items-center">
                        <button type="button" class="btn btn-quaternary d-inline-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#attestatoModal">
                            <i class="bi bi-patch-check"></i> Attestato
                        </button>
                        <button type="button" class="btn btn-senary d-inline-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#cvModal">
                            <i class="bi bi-file-earmark-person"></i> CV
                        </button>
                    </div>
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

    <!-- Modal Attestato -->
    <div class="modal fade modal-custom-terminal" id="attestatoModal" tabindex="-1" aria-labelledby="attestatoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header d-flex justify-content-between align-items-center">
                    <div class="modal-title d-flex align-items-center gap-2">
                        <span class="text-comment">./</span><span class="text-keyword">view</span><span class="text-variable">('attestato')</span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <a href="https://www.credential.net/2ce81450-7049-4f32-8e8b-941033409383" target="_blank" rel="noopener noreferrer" class="btn btn-quaternary btn-sm d-none d-sm-inline-flex align-items-center gap-1">
                            credential.net &#x2197;
                        </a>
                        <button type="button" class="btn-terminal-close" data-bs-dismiss="modal" aria-label="Close">[x]</button>
                    </div>
                </div>
                <div class="modal-body p-3 p-md-4 d-flex justify-content-center align-items-center">
                    <a href="https://www.credential.net/2ce81450-7049-4f32-8e8b-941033409383" target="_blank" rel="noopener noreferrer" title="Clicca per verificare su credential.net" class="d-inline-block text-center">
                        <img
                            src="https://api.accredible.com/v1/frontend/credential_website_embed_image/certificate/198063936"
                            alt="Attestato Fabio Spanò"
                            class="img-fluid modal-certificate-img"
                            loading="lazy">
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal CV -->
    <div class="modal fade modal-custom-terminal" id="cvModal" tabindex="-1" aria-labelledby="cvModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header d-flex justify-content-between align-items-center">
                    <div class="modal-title d-flex align-items-center gap-2">
                        <span class="text-comment">./</span><span class="text-keyword">view</span><span class="text-variable">('cv.pdf')</span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        @if (file_exists(public_path('docs/cv.pdf')))
                            <a href="{{ asset('docs/cv.pdf') }}" target="_blank" download class="btn btn-senary btn-sm d-none d-sm-inline-flex align-items-center gap-1">
                                Download <i class="bi bi-download"></i>
                            </a>
                        @endif
                        <button type="button" class="btn-terminal-close" data-bs-dismiss="modal" aria-label="Close">[x]</button>
                    </div>
                </div>
                <div class="modal-body p-0">
                    @if (file_exists(public_path('docs/cv.pdf')))
                        <iframe
                            src="{{ asset('docs/cv.pdf') }}"
                            class="modal-document-frame"
                            frameborder="0"
                            title="CV Fabio Spanò">
                        </iframe>
                    @else
                        <div class="cv-placeholder-container d-flex flex-column align-items-center justify-content-center p-5 text-center">
                            <i class="bi bi-file-earmark-pdf mb-3" style="font-size: 3rem; color: var(--color-p);"></i>
                            <p class="mb-2"><span class="text-comment">// Inserisci il file PDF del tuo CV in:</span></p>
                            <code class="px-3 py-2 text-variable" style="background: var(--color-bg); border: 1px solid var(--color-nav-bord);">public/docs/cv.pdf</code>
                            <p class="mt-3 text-secondary small">Una volta inserito il file, verrà visualizzato direttamente in questo riquadro.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-layout>