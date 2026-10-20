<?php

use App\Models\Post;
use App\Models\Project;
use App\View\Components\ProjectCard;
use Illuminate\Http\Client\Request as HttpRequest;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    config(['services.resend.key' => 'test-resend-api-key']);
    Http::preventStrayRequests();
});

it('serves the home page', function () {
    $this->get('/')->assertOk();
});

it('shows projects on the home page', function () {
    $project = Project::factory()->create(['title' => 'Laravel Terminal Portfolio']);

    $this->get('/')->assertOk()->assertSee($project->title);
});

it('cycles project button styles continuously without duplicate adjacent styles', function () {
    $p1 = Project::factory()->create(['tags' => ['python']]);
    $p2 = Project::factory()->create(['tags' => ['java', 'blade']]);
    $p3 = Project::factory()->create(['tags' => ['python', 'php']]);
    $p4 = Project::factory()->create(['tags' => ['blade']]);

    ProjectCard::resetCycle();
    $card1 = new ProjectCard($p1, 0);
    $card2 = new ProjectCard($p2, 1);
    $card3 = new ProjectCard($p3, 2);
    $card4 = new ProjectCard($p4, 3);

    expect($card1->buttonClass())->not->toBe($card2->buttonClass())
        ->and($card2->buttonClass())->not->toBe($card3->buttonClass())
        ->and($card3->buttonClass())->not->toBe($card4->buttonClass());
});

it('serves the blog index', function () {
    $this->get('/blog')->assertOk()->assertSee('Blog // Fabio Spanò');
});

it('shows posts on the blog index', function () {
    $post = Post::factory()->create();

    $this->get('/blog')->assertOk()->assertSee($post->title);
});

it('serves a single post by slug', function () {
    $post = Post::factory()->create(['content' => 'This is the full article body text.']);

    $this->get("/blog/{$post->slug}")->assertOk()->assertSee('This is the full article body text.');
});

it('returns a custom 404 for a missing post', function () {
    $this->get('/blog/not-a-real-slug')->assertNotFound()->assertSee('404 // Page not found');
});

it('serves the contact page', function () {
    $this->get('/contact')
        ->assertOk()
        ->assertSee('Send message')
        ->assertSee('action="'.route('contact.send').'"', false);
});

it('returns a valid sitemap', function () {
    $post = Post::factory()->create();

    $response = $this->get('/sitemap.xml')->assertOk();

    expect($response->headers->get('Content-Type'))->toContain('application/xml');
    expect($response->getContent())->toContain('/blog')
        ->toContain('/contact')
        ->toContain($post->slug);
});

it('sends contact email on valid submission without honeypot', function () {
    Http::fake([
        'https://api.resend.com/emails' => Http::response(['id' => 'test-email-id'], 200),
    ]);

    $this->post('/contact', [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'message' => 'This is a genuine inquiry message about your projects.',
    ])->assertRedirect(route('contact'))
        ->assertSessionHas('success');

    Http::assertSent(function (HttpRequest $request) {
        return $request->url() === 'https://api.resend.com/emails'
            && $request['to'] === ['fabiospanoo@outlook.it']
            && $request['reply_to'] === ['john@example.com']
            && $request['subject'] === 'New message from contact form'
            && str_contains($request['html'], 'This is a genuine inquiry message about your projects.');
    });
});

it('silently discards contact submission when honeypot is filled', function () {
    $this->post('/contact', [
        '_hp_company' => 'Bot Spammer Inc.',
        'name' => 'Bot',
        'email' => 'bot@example.com',
        'message' => 'Spam spam spam marketing message.',
    ])->assertRedirect(route('contact'))
        ->assertSessionHas('success');

    Http::assertNothingSent();
});

it('sends contact email with valid attachment under 2mb', function () {
    Http::fake([
        'https://api.resend.com/emails' => Http::response(['id' => 'test-email-id'], 200),
    ]);

    $file = UploadedFile::fake()->create('specs.pdf', 500, 'application/pdf');

    $this->post('/contact', [
        'name' => 'Jane Client',
        'email' => 'jane@example.com',
        'message' => 'Please find attached the project specs for our inquiry.',
        'attachment' => $file,
    ])->assertRedirect(route('contact'))
        ->assertSessionHas('success');

    Http::assertSent(function (HttpRequest $request) {
        return $request['to'] === ['fabiospanoo@outlook.it']
            && str_contains($request['html'], 'Jane Client')
            && $request['attachments'][0]['filename'] === 'specs.pdf'
            && base64_decode($request['attachments'][0]['content'], true) !== false;
    });
});

it('rejects contact attachment exceeding 2mb', function () {
    $largeFile = UploadedFile::fake()->create('large-archive.zip', 2500, 'application/zip');

    $this->from('/contact')->post('/contact', [
        'name' => 'Heavy Sender',
        'email' => 'heavy@example.com',
        'message' => 'Here is a very large file that should not pass.',
        'attachment' => $largeFile,
    ])->assertRedirect('/contact')
        ->assertSessionHasErrors('attachment');

    Http::assertNothingSent();
});

it('rejects contact attachment with disallowed extension', function () {
    $badFile = UploadedFile::fake()->create('malicious.exe', 100, 'application/x-msdownload');

    $this->from('/contact')->post('/contact', [
        'name' => 'Bad Actor',
        'email' => 'actor@example.com',
        'message' => 'Try running this executable please.',
        'attachment' => $badFile,
    ])->assertRedirect('/contact')
        ->assertSessionHasErrors('attachment');

    Http::assertNothingSent();
});

it('accepts json contact submission with attachment and returns ok', function () {
    Http::fake([
        'https://api.resend.com/emails' => Http::response(['id' => 'test-email-id'], 200),
    ]);

    $file = UploadedFile::fake()->create('project-details.pdf', 300, 'application/pdf');

    $response = $this->postJson('/contact', [
        'name' => 'Ajax Sender',
        'email' => 'ajax@example.com',
        'message' => 'This is an asynchronous contact inquiry message.',
        'attachment' => $file,
    ]);

    $response->assertOk()
        ->assertJson(['ok' => true]);

    Http::assertSent(function (HttpRequest $request) {
        return $request['to'] === ['fabiospanoo@outlook.it']
            && str_contains($request['html'], 'Ajax Sender')
            && $request['attachments'][0]['filename'] === 'project-details.pdf';
    });
});

it('accepts json contact submission when honeypot is filled and returns ok without sending mail', function () {
    $response = $this->postJson('/contact', [
        '_hp_company' => 'SpamBot',
        'name' => 'Spammer',
        'email' => 'spam@example.com',
        'message' => 'Spam spam spam spam spam spam.',
    ]);

    $response->assertOk()
        ->assertJson(['ok' => true]);

    Http::assertNothingSent();
});

it('falls back to Formspree when Resend is unavailable', function () {
    config(['services.resend.key' => null]);
    config(['services.formspree.id' => 'test-form-id']);

    Http::fake([
        'https://formspree.io/f/test-form-id' => Http::response(['ok' => true], 200),
    ]);

    $response = $this->postJson('/contact', [
        'name' => 'Fallback Sender',
        'email' => 'fallback@example.com',
        'message' => 'This message should go through Formspree fallback.',
    ]);

    $response->assertOk()
        ->assertJson(['ok' => true]);

    Http::assertSent(function (HttpRequest $request) {
        return $request->url() === 'https://formspree.io/f/test-form-id'
            && $request['email'] === 'fallback@example.com';
    });
});

it('gracefully fails without leaking when both email services fail', function () {
    config(['services.resend.key' => null]);
    config(['services.formspree.id' => 'test-form-id']);

    Http::fake([
        'https://formspree.io/f/test-form-id' => Http::response(['error' => 'Form not found'], 404),
    ]);

    $response = $this->postJson('/contact', [
        'name' => 'Both Failed',
        'email' => 'failed@example.com',
        'message' => 'This message tests complete failure handling.',
    ]);

    $response->assertServerError()
        ->assertJson([
            'message' => 'Unable to send message at this time. Please try again later or email me directly at fabiospanoo@outlook.it.',
        ])
        ->assertJsonMissing(['error' => 'Form not found']);
});

it('renders schema.org structured data and hreflang tags on home page', function () {
    $project = Project::factory()->create([
        'title' => 'Sample Project',
        'url' => 'https://github.com/fabiospanoo/sample',
        'tags' => ['java', 'spring'],
    ]);

    $response = $this->get('/')->assertOk();
    $content = $response->getContent();

    preg_match('/<script type="application\/ld\+json">(.*?)<\/script>/s', $content, $matches);
    expect($matches)->not->toBeEmpty();
    $decoded = json_decode($matches[1], true);
    expect(json_last_error())->toBe(JSON_ERROR_NONE)
        ->and($decoded)->toHaveKey('@graph');

    expect($content)->toContain('<script type="application/ld+json">')
        ->toContain('"@type": "Person"')
        ->toContain('"name": "Fabio Spanò"')
        ->toContain('"@type": "WebSite"')
        ->toContain('"@type": "ProfilePage"')
        ->toContain('"@type": "ItemList"')
        ->toContain('"@type": "SoftwareSourceCode"')
        ->toContain('Sample Project')
        ->toContain('hreflang="en"')
        ->toContain('hreflang="x-default"')
        ->toContain('property="og:locale" content="en_US"')
        ->toContain('property="og:locale:alternate" content="it_IT"');
});

it('renders schema.org blog posting structured data on article page', function () {
    $post = Post::factory()->create([
        'title' => 'Understanding Agentic AI',
        'category' => 'AI',
    ]);

    $response = $this->get("/blog/{$post->slug}")->assertOk();
    $content = $response->getContent();

    preg_match('/<script type="application\/ld\+json">(.*?)<\/script>/s', $content, $matches);
    expect($matches)->not->toBeEmpty();
    $decoded = json_decode($matches[1], true);
    expect(json_last_error())->toBe(JSON_ERROR_NONE)
        ->and($decoded)->toHaveKey('@graph');

    expect($content)->toContain('<script type="application/ld+json">')
        ->toContain('"@type": "BlogPosting"')
        ->toContain('Understanding Agentic AI')
        ->toContain('"articleSection": "AI"');
});

it('renders schema.org contact page structured data on contact page', function () {
    $response = $this->get('/contact')->assertOk();
    $content = $response->getContent();

    preg_match('/<script type="application\/ld\+json">(.*?)<\/script>/s', $content, $matches);
    expect($matches)->not->toBeEmpty();
    $decoded = json_decode($matches[1], true);
    expect(json_last_error())->toBe(JSON_ERROR_NONE)
        ->and($decoded)->toHaveKey('@graph');

    expect($content)->toContain('<script type="application/ld+json">')
        ->toContain('"@type": "ContactPage"');
});
