<?php

namespace Database\Seeders;

use App\Models\Post;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $posts = [
            [
                'title' => 'Building a terminal-style portfolio with Laravel and Blade',
                'slug' => 'terminal-style-portfolio-laravel-blade',
                'category' => 'Web Development',
                'content' => "Why terminal themes?\n\nA portfolio is the first thing a developer shows. I wanted something that reflects how I think: monospace, comments, variables, green prompts. Blade made it easy to reuse components (layout, navbar, project card) and keep every page consistent.\n\nThe admin area lives at /login, it is not linked anywhere on the site, and it requires both a password and a TOTP code. Everything you edit there ends up in plain Blade views rendered server-side - no JavaScript framework needed.",
            ],
            [
                'title' => 'Agentic AI: giving agents real tasks',
                'slug' => 'agentic-ai-real-tasks',
                'category' => 'Agentic AI',
                'content' => "Writing is the easy part: models have read everything. The interesting part is letting an agent act - run code, edit files, verify results and report back.\n\nIn my projects I use agents to scaffold Laravel features, explore unknown codebases and keep tasks in a todo list while I work. The loop is: plan, execute, verify, iterate. The agent checks its own output against the real environment instead of assuming success.",
            ],
        ];

        foreach ($posts as $data) {
            Post::updateOrCreate(['slug' => $data['slug']], $data);
        }
    }
}
