<?php declare(strict_types=1);

namespace Soyhuce\EloquentExtended\Tests;

use InvalidArgumentException;
use PDO;
use Soyhuce\EloquentExtended\Tests\Fixtures\Post;
use Soyhuce\EloquentExtended\Tests\Fixtures\User;

/**
 * @coversDefaultClass \Soyhuce\EloquentExtended\Mixins\Ordering
 */
class OrderingTest extends TestCase
{
    /**
     * @test
     * @covers ::preventInvalidDirection
     */
    public function ascAndDescAreValid(): void
    {
        User::query()->preventInvalidDirection('asc');
        User::query()->preventInvalidDirection('desc');

        $this->assertTrue(true);
    }

    /**
     * @test
     * @covers ::preventInvalidDirection
     */
    public function otherDirectionIsInvalid(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Order direction must be "asc" or "desc".');

        User::query()->preventInvalidDirection('foo');
    }

    /**
     * @test
     * @covers ::orderByNullsLast
     */
    public function orderByNullLast(): void
    {
        $sqliteVersion = User::query()->getConnection()->getPdo()->getAttribute(PDO::ATTR_SERVER_VERSION);

        if (version_compare($sqliteVersion, '3.31', '<')) {
            $this->markTestSkipped('Sqlite < 3.31 does not support nulls last');
        }

        $admin = User::factory()->createOne(['role' => 'admin']);
        $user = User::factory()->createOne(['role' => 'user']);
        $none = User::factory()->createOne(['role' => null]);

        $this->assertEquals(
            [$admin->id, $user->id, $none->id],
            User::query()->orderByNullsLast('role')->pluck('id')->all()
        );

        $this->assertEquals(
            [$user->id, $admin->id, $none->id],
            User::query()->orderByNullsLast('role', 'desc')->pluck('id')->all()
        );
    }

    /**
     * @test
     * @covers ::orderByRawNullsLast
     */
    public function orderByRawNullsLast(): void
    {
        $sqliteVersion = User::query()->getConnection()->getPdo()->getAttribute(PDO::ATTR_SERVER_VERSION);

        if (version_compare($sqliteVersion, '3.31', '<')) {
            $this->markTestSkipped('Sqlite < 3.31 does not support nulls last');
        }

        $admin = User::factory()->createOne(['role' => 'admin']);
        $user = User::factory()->createOne(['role' => 'user']);
        $none = User::factory()->createOne(['role' => null]);

        $this->assertEquals(
            [$admin->id, $user->id, $none->id],
            User::query()->orderByRawNullsLast('UPPER("role")')->pluck('id')->all()
        );

        $this->assertEquals(
            [$user->id, $admin->id, $none->id],
            User::query()->orderByRawNullsLast('UPPER("role") desc')->pluck('id')->all()
        );
    }

    /**
     * @test
     * @covers ::orderByExists
     */
    public function orderByExists(): void
    {
        $first = User::factory()->has(Post::factory()->published())->createOne();
        $second = User::factory()->createOne();
        $third = User::factory()->has(Post::factory(2)->published(false))->createOne();

        $this->assertEquals(
            [$second->id, $first->id, $third->id],
            User::query()->orderByExists('posts')->orderBy('id')->pluck('id')->all()
        );

        $this->assertEquals(
            [$first->id, $third->id, $second->id],
            User::query()->orderByExistsDesc('posts')->orderBy('id')->pluck('id')->all()
        );

        $this->assertEquals(
            [$second->id, $third->id, $first->id],
            User::query()
                ->orderByExists('posts', fn ($query) => $query->where('published', true))
                ->orderBy('id')
                ->pluck('id')
                ->all()
        );

        $this->assertEquals(
            [$first->id, $second->id, $third->id],
            User::query()
                ->orderByExistsDesc('posts', fn ($query) => $query->where('published', true))
                ->orderBy('id')
                ->pluck('id')
                ->all()
        );
    }

    /**
     * @test
     * @covers ::orderByExists
     */
    public function orderByCount(): void
    {
        $first = User::factory()->has(Post::factory()->published())->createOne();
        $second = User::factory()->createOne();
        $third = User::factory()->has(Post::factory(2)->published(false))->createOne();

        $this->assertEquals(
            [$second->id, $first->id, $third->id],
            User::query()->orderByCount('posts')->orderBy('id')->pluck('id')->all()
        );

        $this->assertEquals(
            [$third->id, $first->id, $second->id],
            User::query()->orderByCountDesc('posts')->orderBy('id')->pluck('id')->all()
        );

        $this->assertEquals(
            [$second->id, $third->id, $first->id],
            User::query()
                ->orderByCount('posts', fn ($query) => $query->where('published', true))
                ->orderBy('id')
                ->pluck('id')
                ->all()
        );

        $this->assertEquals(
            [$first->id, $second->id, $third->id],
            User::query()
                ->orderByCountDesc('posts', fn ($query) => $query->where('published', true))
                ->orderBy('id')
                ->pluck('id')
                ->all()
        );
    }

    /**
     * @test
     * @covers ::orderByExists
     */
    public function orderBySum(): void
    {
        $first = User::factory()->has(Post::factory()->published())->createOne();
        $second = User::factory()->createOne();
        $third = User::factory()->has(Post::factory(2)->published(false))->createOne();

        $this->assertEquals(
            [$second->id, $first->id, $third->id],
            User::query()->withSum('posts', 'id')->orderBySum('posts', 'id')->orderBy('id')->pluck('id')->all()
        );

        $this->assertEquals(
            [$third->id, $first->id, $second->id],
            User::query()->orderBySumDesc('posts', 'id')->orderBy('id')->pluck('id')->all()
        );

        $this->assertEquals(
            [$second->id, $third->id, $first->id],
            User::query()
                ->orderBySum('posts', 'id', fn ($query) => $query->where('published', true))
                ->orderBy('id')
                ->pluck('id')
                ->all()
        );

        $this->assertEquals(
            [$first->id, $second->id, $third->id],
            User::query()
                ->orderBySumDesc('posts', 'id', fn ($query) => $query->where('published', true))
                ->orderBy('id')
                ->pluck('id')
                ->all()
        );
    }

    /**
     * @test
     * @covers ::orderByExists
     */
    public function orderByMin(): void
    {
        $first = User::factory()->has(Post::factory()->published(false))->createOne();
        $second = User::factory()->createOne();
        $third = User::factory()->has(Post::factory(2)->published())->createOne();
        Post::factory()->for($first)->published(false)->createOne();
        Post::factory()->published()->for($second)->createOne();

        $this->assertEquals(
            [$first->id, $third->id, $second->id],
            User::query()->orderByMin('posts', 'id')->pluck('id')->all()
        );

        $this->assertEquals(
            [$second->id, $third->id, $first->id],
            User::query()->orderByMinDesc('posts', 'id')->pluck('id')->all()
        );

        $this->assertEquals(
            [$first->id, $third->id, $second->id],
            User::query()
                ->orderByMin('posts', 'id', fn ($query) => $query->where('published', true))
                ->pluck('id')
                ->all()
        );

        $this->assertEquals(
            [$second->id, $third->id, $first->id],
            User::query()
                ->orderByMinDesc('posts', 'id', fn ($query) => $query->where('published', true))
                ->pluck('id')
                ->all()
        );
    }

    /**
     * @test
     * @covers ::orderByExists
     */
    public function orderByMax(): void
    {
        $first = User::factory()->has(Post::factory()->published(false))->createOne();
        $second = User::factory()->createOne();
        $third = User::factory()->has(Post::factory(2)->published())->createOne();
        Post::factory()->for($first)->published(false)->createOne();
        Post::factory()->published()->for($second)->createOne();

        $this->assertEquals(
            [$third->id, $first->id, $second->id],
            User::query()->orderByMax('posts', 'id')->pluck('id')->all()
        );

        $this->assertEquals(
            [$second->id, $first->id, $third->id],
            User::query()->orderByMaxDesc('posts', 'id')->pluck('id')->all()
        );

        $this->assertEquals(
            [$first->id, $third->id, $second->id],
            User::query()
                ->orderByMax('posts', 'id', fn ($query) => $query->where('published', true))
                ->pluck('id')
                ->all()
        );

        $this->assertEquals(
            [$second->id, $third->id, $first->id],
            User::query()
                ->orderByMaxDesc('posts', 'id', fn ($query) => $query->where('published', true))
                ->pluck('id')
                ->all()
        );
    }
}
