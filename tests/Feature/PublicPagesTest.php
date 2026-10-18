<?php

use App\Mail\ContactMessage;
use App\Models\Post;
use App\Models\Project;
use App\View\Components\ProjectCard;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;

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
    $this->get('/contact')->assertOk()->assertSee('Send message');
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
    Mail::fake();

    $this->post('/contact', [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'message' => 'This is a genuine inquiry message about your projects.',
    ])->assertRedirect(route('contact'))
        ->assertSessionHas('success');

    Mail::assertSentCount(1);
});

it('silently discards contact submission when honeypot is filled', function () {
    Mail::fake();

    $this->post('/contact', [
        '_hp_company' => 'Bot Spammer Inc.',
        'name' => 'Bot',
        'email' => 'bot@example.com',
        'message' => 'Spam spam spam marketing message.',
    ])->assertRedirect(route('contact'))
        ->assertSessionHas('success');

    Mail::assertNothingSent();
});

it('sends contact email with valid attachment under 2mb', function () {
    Mail::fake();

    $file = UploadedFile::fake()->create('specs.pdf', 500, 'application/pdf');

    $this->post('/contact', [
        'name' => 'Jane Client',
        'email' => 'jane@example.com',
        'message' => 'Please find attached the project specs for our inquiry.',
        'attachment' => $file,
    ])->assertRedirect(route('contact'))
        ->assertSessionHas('success');

    Mail::assertSent(ContactMessage::class, function (ContactMessage $mail) {
        return $mail->contactData['name'] === 'Jane Client'
            && ! empty($mail->attachmentData)
            && $mail->attachmentData['name'] === 'specs.pdf'
            && count($mail->attachments()) === 1;
    });
});

it('rejects contact attachment exceeding 2mb', function () {
    Mail::fake();

    $largeFile = UploadedFile::fake()->create('large-archive.zip', 2500, 'application/zip');

    $this->from('/contact')->post('/contact', [
        'name' => 'Heavy Sender',
        'email' => 'heavy@example.com',
        'message' => 'Here is a very large file that should not pass.',
        'attachment' => $largeFile,
    ])->assertRedirect('/contact')
        ->assertSessionHasErrors('attachment');

    Mail::assertNothingSent();
});

it('rejects contact attachment with disallowed extension', function () {
    Mail::fake();

    $badFile = UploadedFile::fake()->create('malicious.exe', 100, 'application/x-msdownload');

    $this->from('/contact')->post('/contact', [
        'name' => 'Bad Actor',
        'email' => 'actor@example.com',
        'message' => 'Try running this executable please.',
        'attachment' => $badFile,
    ])->assertRedirect('/contact')
        ->assertSessionHasErrors('attachment');

    Mail::assertNothingSent();
});

it('accepts json contact submission with attachment and returns ok', function () {
    Mail::fake();

    $file = UploadedFile::fake()->create('project-details.pdf', 300, 'application/pdf');

    $response = $this->postJson('/contact', [
        'name' => 'Ajax Sender',
        'email' => 'ajax@example.com',
        'message' => 'This is an asynchronous contact inquiry message.',
        'attachment' => $file,
    ]);

    $response->assertOk()
        ->assertJson(['ok' => true]);

    Mail::assertSent(ContactMessage::class, function (ContactMessage $mail) {
        return $mail->contactData['name'] === 'Ajax Sender'
            && ! empty($mail->attachmentData)
            && $mail->attachmentData['name'] === 'project-details.pdf';
    });
});

it('accepts json contact submission when honeypot is filled and returns ok without sending mail', function () {
    Mail::fake();

    $response = $this->postJson('/contact', [
        '_hp_company' => 'SpamBot',
        'name' => 'Spammer',
        'email' => 'spam@example.com',
        'message' => 'Spam spam spam spam spam spam.',
    ]);

    $response->assertOk()
        ->assertJson(['ok' => true]);

    Mail::assertNothingSent();
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
