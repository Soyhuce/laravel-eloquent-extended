<?php declare(strict_types=1);

namespace Soyhuce\EloquentExtended\Tests;

use Soyhuce\EloquentExtended\Tests\Fixtures\User;

/**
 * @coversDefaultClass \Soyhuce\EloquentExtended\Mixins\Select
 */
class SelectTest extends TestCase
{
    /**
     * @test
     * @covers ::withAnyExists
     */
    public function withAnyExistsReturnCorrectValues(): void
    {
        $author = User::factory()->hasPosts()->createOne();
        $reader = User::factory()->createOne();

        $users = User::query()->withAnyExists(['posts', 'publishedPosts'])->get();

        $this->assertTrue($users->find($author->id)->related_exists);
        $this->assertFalse($users->find($reader->id)->related_exists);
    }

    /**
     * @test
     * @covers ::withImplode
     */
    public function withImplode(): void
    {
        $query = User::query()->withImplode('posts', 'title', ' - ');

        $this->assertEquals(
            'select "users".*, (select string_agg("posts"."title", ?) from "posts" where "users"."id" = "posts"."user_id") as "posts_implode_title" from "users"',
            $query->toSql()
        );
        $this->assertEquals([' - '], $query->getBindings());
    }

    /**
     * @test
     * @covers ::withImplode
     */
    public function withImplodeWithOrder(): void
    {
        $query = User::query()->withImplode('posts', 'title', ' - ', 'id', 'desc');

        $this->assertEquals(
            'select "users".*, (select string_agg("posts"."title", ? order by "posts"."id" desc) from "posts" where "users"."id" = "posts"."user_id") as "posts_implode_title" from "users"',
            $query->toSql()
        );
        $this->assertEquals([' - '], $query->getBindings());
    }
}
