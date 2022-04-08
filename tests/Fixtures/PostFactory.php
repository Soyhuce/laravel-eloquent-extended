<?php declare(strict_types=1);

namespace Soyhuce\EloquentExtended\Tests\Fixtures;

use Illuminate\Database\Eloquent\Factories\Factory;

class PostFactory extends Factory
{
    protected $model = Post::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'title' => $this->faker->words(asText: true),
            'published' => $this->faker->boolean,
        ];
    }

    public function published(bool $published = true): self
    {
        return $this->state(['published' => $published]);
    }
}
