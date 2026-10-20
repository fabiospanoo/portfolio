<?php

namespace Database\Seeders;

use App\Models\Project;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $projects = [
            [
                'title' => 'Portfolio Site',
                'text' => 'This portfolio, built with Laravel and a custom terminal-style design theme.',
                'url' => 'https://github.com/fabiospanoo/portfolio',
                'image' => 'https://picsum.photos/seed/fabiospanoo-site/1600/900',
                'tags' => ['blade', 'php'],
            ],
            [
                'title' => 'Project One',
                'text' => 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Est, adipisci aspernatur.',
                'url' => 'https://github.com/fabiospanoo/project-one',
                'image' => 'https://picsum.photos/seed/fabiospanoo-one/1600/900',
                'tags' => ['java'],
            ],
            [
                'title' => 'Project Two',
                'text' => 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Est, adipisci aspernatur.',
                'url' => 'https://github.com/fabiospanoo/project-two',
                'image' => 'https://picsum.photos/seed/fabiospanoo-two/1600/900',
                'tags' => ['python'],
            ],
            [
                'title' => 'Project Three',
                'text' => 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Est, adipisci aspernatur.',
                'url' => 'https://github.com/fabiospanoo/project-three',
                'image' => 'https://picsum.photos/seed/fabiospanoo-three/1600/900',
                'tags' => ['java', 'blade'],
            ],
            [
                'title' => 'Project Four',
                'text' => 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Est, adipisci aspernatur.',
                'url' => 'https://github.com/fabiospanoo/project-four',
                'image' => 'https://picsum.photos/seed/fabiospanoo-four/1600/900',
                'tags' => ['python', 'php'],
            ],
            [
                'title' => 'Project Five',
                'text' => 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Est, adipisci aspernatur.',
                'url' => 'https://github.com/fabiospanoo/project-five',
                'image' => 'https://picsum.photos/seed/fabiospanoo-five/1600/900',
                'tags' => ['blade'],
            ],
        ];

        foreach ($projects as $data) {
            Project::updateOrCreate(
                ['title' => $data['title']],
                $data
            );
        }
    }
}
