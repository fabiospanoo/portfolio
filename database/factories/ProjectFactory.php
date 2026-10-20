<?php

namespace Database\Factories;

use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProjectFactory extends Factory
{
    protected $model = Project::class;

    public function definition(): array
    {
        return [
            'title' => fake()->words(3, true),
            'text' => fake()->paragraph(),
            'url' => fake()->url(),
            'image' => null,
            'tags' => ['php', 'laravel', 'blade'],
        ];
    }
}
