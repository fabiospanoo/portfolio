@props([
    'title' => null,
    'metaDescription' => null,
    'metaType' => 'website',
    'metaImage' => null,
    'post' => null,
    'projects' => null,
])

@php
    $siteName = config('app.name', 'Fabio Spanò');
    $pageTitle = $title ?? $siteName;
    $canonicalHost = config('site.canonical_host');
    $scheme = config('site.app_scheme', 'https');
    $baseUrl = $canonicalHost ? "{$scheme}://{$canonicalHost}" : url('/');
    $canonical = $canonicalHost
        ? "{$scheme}://{$canonicalHost}".request()->getRequestUri()
        : request()->url();

    $description = $metaDescription
        ?? 'Personal web developer portfolio. Web development projects and applications.';

    $image = $metaImage ? url($metaImage) : url('images/logo.png');

    $personId = "{$baseUrl}/#person";
    $websiteId = "{$baseUrl}/#website";

    $graph = [
        [
            '@type' => 'Person',
            '@id' => $personId,
            'name' => 'Fabio Spanò',
            'alternateName' => 'fabiospanoo',
            'url' => $baseUrl,
            'image' => $image,
            'jobTitle' => 'Web Developer',
            'description' => 'Web developer specializing in modern web applications.',
            'alumniOf' => [
                '@type' => 'EducationalOrganization',
                'name' => 'AuLab',
            ],
            'sameAs' => [
                'https://github.com/fabiospanoo',
                'https://www.linkedin.com/in/fabiospanoo',
            ],
            'knowsAbout' => [
                'PHP',
                'Laravel',
                'Blade',
                'JavaScript',
                'HTML',
                'CSS',
                'Web Development',
            ],
        ],
        [
            '@type' => 'WebSite',
            '@id' => $websiteId,
            'url' => $baseUrl,
            'name' => 'Fabio Spanò Portfolio',
            'description' => 'Portfolio of Fabio Spanò, Web Developer.',
            'publisher' => [
                '@id' => $personId,
            ],
            'inLanguage' => ['en', 'it'],
        ],
    ];

    if ($metaType === 'article' || $post) {
        $articleSchema = [
            '@type' => 'BlogPosting',
            '@id' => "{$canonical}#article",
            'isPartOf' => ['@id' => $websiteId],
            'headline' => $post ? $post->title : $pageTitle,
            'description' => $description,
            'url' => $canonical,
            'inLanguage' => 'en',
            'mainEntityOfPage' => [
                '@type' => 'WebPage',
                '@id' => $canonical,
            ],
            'author' => ['@id' => $personId],
            'publisher' => ['@id' => $personId],
            'image' => $image,
        ];

        if ($post) {
            if ($post->created_at) {
                $articleSchema['datePublished'] = $post->created_at->toIso8601String();
            }
            if ($post->updated_at ?? $post->created_at) {
                $articleSchema['dateModified'] = ($post->updated_at ?? $post->created_at)->toIso8601String();
            }
            if (! empty($post->category)) {
                $articleSchema['articleSection'] = $post->category;
            }
        }

        $graph[] = $articleSchema;
    } elseif (request()->is('contact*')) {
        $graph[] = [
            '@type' => 'ContactPage',
            '@id' => "{$canonical}#webpage",
            'url' => $canonical,
            'name' => $pageTitle,
            'description' => $description,
            'isPartOf' => ['@id' => $websiteId],
            'mainEntity' => ['@id' => $personId],
        ];
    } else {
        $graph[] = [
            '@type' => 'ProfilePage',
            '@id' => "{$canonical}#webpage",
            'url' => $canonical,
            'name' => $pageTitle,
            'description' => $description,
            'isPartOf' => ['@id' => $websiteId],
            'mainEntity' => ['@id' => $personId],
        ];

        if ($projects && method_exists($projects, 'isNotEmpty') && $projects->isNotEmpty()) {
            $itemList = [
                '@type' => 'ItemList',
                'name' => 'Projects by Fabio Spanò',
                'itemListElement' => [],
            ];
            foreach ($projects as $idx => $p) {
                $item = [
                    '@type' => 'SoftwareSourceCode',
                    'name' => $p->title,
                    'description' => $p->text,
                    'author' => ['@id' => $personId],
                ];
                if (! empty($p->url)) {
                    $item['codeRepository'] = $p->url;
                    $item['url'] = $p->url;
                }
                if (! empty($p->tags)) {
                    $item['programmingLanguage'] = is_array($p->tags) ? implode(', ', $p->tags) : (string) $p->tags;
                }
                $itemList['itemListElement'][] = [
                    '@type' => 'ListItem',
                    'position' => $idx + 1,
                    'item' => $item,
                ];
            }
            $graph[] = $itemList;
        }
    }

    $schemaJson = json_encode([
        '@context' => 'https://schema.org',
        '@graph' => $graph,
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
@endphp

<meta name="description" content="{{ $description }}">
<meta name="robots" content="index, follow">
<link rel="canonical" href="{{ $canonical }}">
<link rel="alternate" hreflang="en" href="{{ $canonical }}">
<link rel="alternate" hreflang="x-default" href="{{ $canonical }}">

<meta property="og:site_name" content="{{ $siteName }}">
<meta property="og:type" content="{{ $metaType }}">
<meta property="og:title" content="{{ $pageTitle }}">
<meta property="og:description" content="{{ $description }}">
<meta property="og:url" content="{{ $canonical }}">
<meta property="og:image" content="{{ $image }}">
<meta property="og:locale" content="en_US">
<meta property="og:locale:alternate" content="it_IT">

<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $pageTitle }}">
<meta name="twitter:description" content="{{ $description }}">
<meta name="twitter:image" content="{{ $image }}">

<script type="application/ld+json">
{!! $schemaJson !!}
</script>