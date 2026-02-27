<?php declare(strict_types=1);

namespace Soyhuce\EloquentExtended\Tests;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Soyhuce\EloquentExtended\Tests\Fixtures\User;

#[CoversClass(\Soyhuce\EloquentExtended\Mixins\Result::class)]
class ResultTest extends TestCase
{
    #[Test]
    public function modelCanBeRandomlyChosen(): void
    {
        User::factory(100)->create();

        for ($i = 0; $i < 10; $i++) {
            $first = User::query()->random();
            $second = User::query()->random();

            if (!$first->is($second)) {
                $this->assertTrue(true);

                return;
            }
        }

        $this->assertTrue(false, 'Failed to get 2 randoms users');
    }

    #[Test]
    public function resultCanBeNull(): void
    {
        $this->assertNull(User::query()->random());
    }
}
