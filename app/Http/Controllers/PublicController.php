<?php

namespace App\Http\Controllers;

use App\Mail\ContactMessage;
use App\Models\Post;
use App\Models\Project;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class PublicController extends Controller
{
    public function projects(): View
    {
        return view('projects', [
            'projects' => Project::orderByDesc('created_at')->orderByDesc('id')->get(),
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
            $attachmentPath = $file->getRealPath();

            if ($attachmentPath === false) {
                Log::error('Unable to read contact form attachment path.');

                if ($request->expectsJson()) {
                    return response()->json([
                        'message' => 'Unable to read the uploaded attachment. Please try again.',
                    ], 422);
                }

                return redirect()->route('contact')->withErrors([
                    'attachment' => 'Unable to read the uploaded attachment. Please try again.',
                ]);
            }

            $attachmentData = [
                'path' => $attachmentPath,
                'name' => $file->getClientOriginalName(),
                'mime' => $file->getClientMimeType() ?: $file->getMimeType(),
            ];
        }

        $contactData = [
            'name' => (string) $validated['name'],
            'email' => (string) $validated['email'],
            'message' => (string) $validated['message'],
        ];

        $resendKey = config('services.resend.key');
        $formspreeId = config('services.formspree.id');

        $sent = false;

        if (filled($resendKey)) {
            try {
                $mail = new ContactMessage($contactData, $attachmentData);
                $payload = [
                    'from' => sprintf('%s <%s>', config('mail.from.name'), config('mail.from.address')),
                    'to' => ['fabiospanoo@outlook.it'],
                    'reply_to' => [$contactData['email']],
                    'subject' => $mail->envelope()->subject,
                    'html' => $mail->render(),
                ];

                if ($attachmentData !== null && file_exists($attachmentData['path'])) {
                    $fileContents = file_get_contents($attachmentData['path']);
                    if ($fileContents !== false) {
                        $payload['attachments'] = [[
                            'filename' => $attachmentData['name'],
                            'content' => base64_encode($fileContents),
                        ]];
                    }
                }

                Http::timeout(15)
                    ->withToken($resendKey)
                    ->post('https://api.resend.com/emails', $payload)
                    ->throw();

                $sent = true;
            } catch (\Throwable $e) {
                Log::warning('Resend email delivery failed, falling back: '.$e->getMessage());
            }
        }

        if (! $sent && filled($formspreeId)) {
            try {
                $formspreePayload = [
                    'name' => $contactData['name'],
                    'email' => $contactData['email'],
                    'message' => $contactData['message'],
                    '_subject' => 'New message from portfolio: '.$contactData['name'],
                ];

                $response = Http::timeout(15)
                    ->withHeaders(['Accept' => 'application/json'])
                    ->post("https://formspree.io/f/{$formspreeId}", $formspreePayload);

                if ($response->successful()) {
                    $sent = true;
                } else {
                    Log::error('Formspree delivery failed: '.$response->body());
                }
            } catch (\Throwable $e) {
                Log::error('Formspree delivery exception: '.$e->getMessage());
            }
        }

        if (! $sent) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Unable to send message at this time. Please try again later or email me directly at fabiospanoo@outlook.it.',
                ], 500);
            }

            return redirect()->route('contact')->withErrors([
                'email' => 'Unable to send message at this time. Please try again later or email me directly at fabiospanoo@outlook.it.',
            ]);
        }

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
