<?php

namespace App\Http\Controllers;

use App\Mail\ContactMessage;
use App\Models\Post;
use App\Models\Project;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class PublicController extends Controller
{
    public function projects(): View
    {
        return view('projects', [
            'projects' => Project::all(),
            'latestPost' => Post::latest()->first(),
        ]);
    }

    public function contact(): View
    {
        return view('contact');
    }

    public function sendContact(Request $request): RedirectResponse|JsonResponse
    {
        if ($request->filled('_hp_company')) {
            if ($request->expectsJson()) {
                return response()->json(['ok' => true]);
            }

            return redirect()->route('contact')->with('success', 'Message sent! I will get back to you soon.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'message' => ['required', 'string', 'min:10'],
            'attachment' => ['nullable', 'file', 'max:2048', 'mimes:pdf,doc,docx,odt,txt,rtf,png,jpg,jpeg,webp,svg,zip'],
        ]);

        $attachmentData = null;
        if ($request->hasFile('attachment') && $request->file('attachment')->isValid()) {
            $file = $request->file('attachment');
            $attachmentData = [
                'path' => $file->getRealPath(),
                'name' => $file->getClientOriginalName(),
                'mime' => $file->getClientMimeType() ?: $file->getMimeType(),
            ];
        }

        $contactData = [
            'name' => (string) $validated['name'],
            'email' => (string) $validated['email'],
            'message' => (string) $validated['message'],
        ];

        Mail::to('fabiospanoo@outlook.it')->send(new ContactMessage($contactData, $attachmentData));

        if ($request->expectsJson()) {
            return response()->json(['ok' => true]);
        }

        return redirect()->route('contact')->with('success', 'Message sent! I will get back to you soon.');
    }

    public function sitemap(): Response
    {
        $scheme = config('site.app_scheme');
        $host = config('site.canonical_host') ?: preg_replace('#^https?://#', '', (string) config('app.url'));
        $base = $scheme.'://'.$host;

        $urls = [
            ['loc' => $base, 'changefreq' => 'daily', 'priority' => '1.0'],
            ['loc' => $base.'/blog', 'changefreq' => 'weekly', 'priority' => '0.8'],
            ['loc' => $base.'/contact', 'changefreq' => 'monthly', 'priority' => '0.5'],
        ];

        foreach (Post::latest()->get() as $post) {
            $urls[] = [
                'loc' => $base.'/blog/'.$post->slug,
                'lastmod' => $post->updated_at?->toAtomString(),
                'changefreq' => 'monthly',
                'priority' => '0.7',
            ];
        }

        $urlset = collect($urls)->map(function (array $url) {
            $tags = '';

            foreach ($url as $key => $value) {
                if ($value === null) {
                    continue;
                }

                $tags .= "        <{$key}>".htmlspecialchars((string) $value, ENT_XML1, 'UTF-8')."</{$key}>\n";
            }

            return "    <url>\n{$tags}    </url>";
        })->implode("\n");

        $xml = '<?xml version="1.0" encoding="UTF-8"?>'."\n"
            .'<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n"
            .$urlset."\n"
            .'</urlset>';

        return response($xml, 200, ['Content-Type' => 'application/xml']);
    }
}
