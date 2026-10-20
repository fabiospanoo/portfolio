<?php

namespace Database\Factories;

use App\Models\Post;
use Illuminate\Database\Eloquent\Factories\Factory;

class PostFactory extends Factory
{
    protected $model = Post::class;

    public function definition(): array
    {
        return [
            'title' => fake()->sentence(4),
            'slug' => fake()->unique()->slug(3),
            'category' => fake()->randomElement(['Web Development', 'Agentic AI', 'Java', 'Laravel', 'DevOps', 'Tutorial']),
            'content' => fake()->paragraphs(3, true),
        ];
    }
}
