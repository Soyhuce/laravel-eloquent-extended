<?php declare(strict_types=1);

namespace Soyhuce\EloquentExtended\Tests;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Soyhuce\EloquentExtended\Tests\Fixtures\User;

/**
 * @coversDefaultClass \Soyhuce\EloquentExtended\Mixins\Misc
 */
class MiscTest extends TestCase
{
    /**
     * @test
     * @covers ::scope
     */
    public function scopeIsApplied(): void
    {
        $admin = User::factory()->createOne(['role' => 'admin']);
        $user = User::factory()->createOne(['role' => 'user']);

        $scope = new class() implements Scope {
            public function apply(Builder $builder, Model $model)
            {
                return $builder->orderByDesc('role');
            }
        };

        $this->assertEquals(
            [$user->id, $admin->id],
            User::query()->scope($scope)->pluck('id')->all()
        );
    }

    /**
     * @test
     * @covers ::scoped
     */
    public function queryCanBeConfiguredViaClosure(): void
    {
        $admin = User::factory()->createOne(['role' => 'admin']);
        $user = User::factory()->createOne(['role' => 'user']);

        $this->assertEquals(
            [$user->id, $admin->id],
            User::query()->scoped(fn (Builder $query) => $query->orderByDesc('role'))->pluck('id')->all()
        );

        $this->assertEquals(
            [$admin->id, $user->id],
            User::query()
                ->scoped(function (Builder $query): void {
                    $query->orderBy('role');
                })
                ->pluck('id')
                ->all()
        );
    }
}
