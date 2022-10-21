<?php declare(strict_types=1);

namespace Soyhuce\EloquentExtended\Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Foundation\Testing\Concerns\InteractsWithDeprecationHandling;
use Orchestra\Testbench\TestCase as Orchestra;
use Soyhuce\EloquentExtended\EloquentExtendedServiceProvider;

/**
 * @coversNothing
 */
class TestCase extends Orchestra
{
    use InteractsWithDeprecationHandling;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutDeprecationHandling();

        $this->loadMigrationsFrom(__DIR__ . '/migrations');
        Factory::guessFactoryNamesUsing(fn (string $modelName) => $modelName . 'Factory');
    }

    protected function getPackageProviders($app)
    {
        return [
            EloquentExtendedServiceProvider::class,
        ];
    }
}
