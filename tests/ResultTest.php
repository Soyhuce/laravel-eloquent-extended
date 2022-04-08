<?php declare(strict_types=1);

namespace Soyhuce\EloquentExtended\Tests;

use Soyhuce\EloquentExtended\Tests\Fixtures\User;

/**
 * @coversDefaultClass \Soyhuce\EloquentExtended\Mixins\Result
 */
class ResultTest extends TestCase
{
    /**
     * @test
     * @covers ::random
     */
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

    /**
     * @test
     * @covers ::random
     */
    public function resultCanBeNull(): void
    {
        $this->assertNull(User::query()->random());
    }
}
