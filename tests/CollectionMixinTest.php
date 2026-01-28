<?php declare(strict_types=1);

namespace Soyhuce\EloquentExtended\Tests;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Soyhuce\EloquentExtended\Tests\Fixtures\User;

#[CoversClass(\Soyhuce\EloquentExtended\Mixins\CollectionMixin::class)]
class CollectionMixinTest extends TestCase
{
    #[Test]
    public function collectionCanLoadMoreAttributes(): void
    {
        /** @var User $author */
        $author = User::factory()->hasPosts()->createOne();
        $reader = User::factory()->createOne();

        Collection::make([$author, $reader])->loadAttributes(
            fn (Builder $query) => $query->withAnyExists(['posts', 'publishedPosts'])
        );

        $this->assertTrue($author->related_exists);
        $this->assertFalse($reader->related_exists);
    }
}
