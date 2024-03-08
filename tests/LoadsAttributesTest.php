<?php declare(strict_types=1);

namespace Soyhuce\EloquentExtended\Tests;

use Illuminate\Database\Eloquent\Builder;
use Soyhuce\EloquentExtended\Tests\Fixtures\User;

/**
 * @coversNothing
 */
class LoadsAttributesTest extends TestCase
{
    /**
     * @test
     */
    public function modelCanLoadMoreAttributes(): void
    {
        /** @var User $author */
        $author = User::factory()->hasPosts()->createOne();

        $author->loadAttributes(
            fn (Builder $query) => $query->withAnyExists(['posts', 'publishedPosts'])
        );

        $this->assertTrue($author->related_exists);
    }
}
