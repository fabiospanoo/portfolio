@props(['title', 'navbarBrand' => null, 'metaDescription' => null, 'metaType' => 'website', 'metaImage' => null])

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
    <x-seo :title="$title" :meta-description="$metaDescription" :meta-type="$metaType" :meta-image="$metaImage" />
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <link rel="stylesheet" href="{{ asset('vendor/aos/aos.css') }}">
    @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body>
    <x-navbar :navbarBrand="$navbarBrand"/>

        <div class="min-vh-100">
            {{$slot}}
        </div>

    <x-footer/>

    <script src="{{ asset('vendor/aos/aos.js') }}"></script>
    <script>
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
