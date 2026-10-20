@props([
    'title',
    'navbarBrand' => null,
    'metaDescription' => null,
    'metaType' => 'website',
    'metaImage' => null,
    'post' => null,
    'projects' => null,
    'codeBackground' => false,
])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
    <x-seo
        :title="$title"
        :meta-description="$metaDescription"
        :meta-type="$metaType"
        :meta-image="$metaImage"
        :post="$post"
        :projects="$projects" />
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <link rel="stylesheet" href="{{ asset('vendor/aos/aos.css') }}">
    @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body @class(['home-code-background' => $codeBackground])>
    @if ($codeBackground)
        <iframe
            class="code-background-frame"
            src="{{ asset('editor-background/index.html') }}"
            title="Sfondo con codice in digitazione"
            aria-hidden="true"
            tabindex="-1"></iframe>
    @endif

    <x-navbar :navbarBrand="$navbarBrand"/>

        <div class="min-vh-100">
            {{$slot}}
        </div>

    <x-footer/>

    <script src="{{ asset('vendor/aos/aos.js') }}"></script>
    <script>
        @if ($codeBackground)
            const updateCodeBackgroundPosition = () => {
                const navbar = document.querySelector('.navbar-custom');
                const footer = document.querySelector('.footer-custom');
                const navbarHeight = navbar.getBoundingClientRect().height;
                const footerHeight = footer.getBoundingClientRect().height;

                document.body.style.setProperty('--code-background-top', `${navbarHeight}px`);
                document.body.style.setProperty('--code-background-height', `${Math.max(0, document.body.scrollHeight - navbarHeight - footerHeight)}px`);
            };

            updateCodeBackgroundPosition();
            const codeBackgroundResizeObserver = new ResizeObserver(updateCodeBackgroundPosition);
            [document.body, document.querySelector('.navbar-custom'), document.querySelector('.footer-custom')].forEach((element) => {
                codeBackgroundResizeObserver.observe(element);
            });
            window.addEventListener('resize', updateCodeBackgroundPosition);
        @endif

        AOS.init({
            once: true,
            duration: 850,
            offset: 30,
            easing: 'ease-out-cubic',
        });
        if (window.__applyProjectsLayout) {
            window.__applyProjectsLayout();
        }
    </script>
</body>
</html>
