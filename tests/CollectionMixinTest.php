<?php declare(strict_types=1);

namespace Soyhuce\EloquentExtended\Tests;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Soyhuce\EloquentExtended\Tests\Fixtures\User;

/**
 * @coversDefaultClass \Soyhuce\EloquentExtended\Mixins\CollectionMixin
 */
class CollectionMixinTest extends TestCase
{
    /**
     * @test
     * @covers ::loadWith
     */
    public function collectionCanLoadMoreAttributes(): void
    {
        /** @var \Soyhuce\EloquentExtended\Tests\Fixtures\User $author */
        $author = User::factory()->hasPosts()->createOne();
        $reader = User::factory()->createOne();

        Collection::make([$author, $reader])->loadAttributes(
            fn (Builder $query) => $query->withAnyExists(['posts', 'publishedPosts'])
        );

        $this->assertTrue($author->related_exists);
        $this->assertFalse($reader->related_exists);
    }
}
