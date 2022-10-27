<?php declare(strict_types=1);

namespace Soyhuce\EloquentExtended\Tests;

use Soyhuce\EloquentExtended\Tests\Fixtures\Post;
use Soyhuce\EloquentExtended\Tests\Fixtures\User;

/**
 * @coversDefaultClass \Soyhuce\EloquentExtended\Tests\Fixtures\Post
 */
class CreationTest extends TestCase
{
    /**
     * @test
     * @covers ::insertModels
     */
    public function modelsAreInserted(): void
    {
        $user = User::factory()->createOne();

        $this->freezeSecond();

        $result = Post::query()->insertModels([
            ['user_id' => $user->id, 'title' => 'Post 1', 'published' => true, 'tags' => ['foo', 'bar']],
            ['user_id' => $user->id, 'title' => 'Post 2', 'published' => false, 'tags' => null],
        ]);

        $this->assertTrue($result);

        $this->assertDatabaseHas(Post::class, [
            'user_id' => $user->id,
            'title' => 'Post 1',
            'published' => true,
            'tags' => $this->castAsJson(['foo', 'bar']),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->assertDatabaseHas(Post::class, [
            'user_id' => $user->id,
            'title' => 'Post 2',
            'published' => false,
            'tags' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * @test
     * @covers ::insertModels
     */
    public function modelsAreUpserted(): void
    {
        $user = User::factory()->createOne();
        $post = Post::factory()->for($user)->createOne(['title' => 'Post 1']);

        $this->freezeSecond();

        $result = Post::query()->upsertModels([
            ['user_id' => $user->id, 'title' => 'Post 1', 'published' => true, 'tags' => ['foo', 'bar']],
            ['user_id' => $user->id, 'title' => 'Post 2', 'published' => false, 'tags' => ['baz', 'qux']],
        ], ['title']);

        $this->assertEquals(2, $result);

        $this->assertDatabaseHas(Post::class, [
            'id' => $post->id,
            'user_id' => $user->id,
            'title' => 'Post 1',
            'published' => true,
            'tags' => $this->castAsJson(['foo', 'bar']),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->assertDatabaseHas(Post::class, [
            'user_id' => $user->id,
            'title' => 'Post 2',
            'published' => false,
            'tags' => $this->castAsJson(['baz', 'qux']),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
