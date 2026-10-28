@props(['navbarBrand' => null])

<nav class="navbar navbar-custom spaced-navbar navbar-expand-lg bg-body-tertiary fixed-top" data-bs-theme="dark">
    <div class="container-fluid">
        <a class="navbar-brand text-keyword" href="{{ route('projects') }}">{{ $navbarBrand }}</a>
        <div class="navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item">
                <a class="nav-link {{ Route::currentRouteName() == 'projects' ? 'active' : '' }}" href="{{ route('projects') }}">Projects</a>
                </li>
                <li class="nav-item">
                <a class="nav-link {{ in_array(Route::currentRouteName(), ['blog.index', 'blog.show']) ? 'active' : '' }}" href="{{ route('blog.index') }}">Blog</a>
                </li>
                <li class="nav-item">
                <a class="nav-link" href="https://github.com/fabiospanoo" target="_blank" rel="noopener noreferrer">Github &#x2197;</a>
                </li>
                <li class="nav-item">
                <a class="nav-link" href="https://www.linkedin.com/in/fabiospanoo" target="_blank" rel="noopener noreferrer">LinkedIn &#x2197;</a>
                </li>
                <li class="nav-item">
                <a class="nav-link {{ Route::currentRouteName() == 'contact' ? 'active' : '' }}" href="{{ route('contact') }}">Contact</a>
                </li>
            </ul>
        </div>
    </div>
</nav>
