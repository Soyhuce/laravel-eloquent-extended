<?php declare(strict_types=1);

namespace Soyhuce\EloquentExtended\Tests;

use ErrorException;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\Concerns\InteractsWithDeprecationHandling;
use Orchestra\Testbench\TestCase as Orchestra;
use Soyhuce\EloquentExtended\EloquentExtendedServiceProvider;
use function in_array;

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
        Model::unguard();
    }

    protected function getPackageProviders($app)
    {
        return [
            EloquentExtendedServiceProvider::class,
        ];
    }

    protected function withoutDeprecationHandling(): static
    {
        if ($this->originalDeprecationHandler == null) {
            $this->originalDeprecationHandler = set_error_handler(function (
                $level,
                $message,
                $file = '',
                $line = 0,
            ): void {
                if (in_array($level, [E_DEPRECATED, E_USER_DEPRECATED], true) || (error_reporting() & $level)) {
                    // Silenced vendor errors
                    if (str_starts_with($file, realpath(__DIR__ . '/../vendor/fakerphp/faker'))) {
                        return;
                    }
                    if (str_starts_with($file, realpath(__DIR__ . '/../vendor/symfony'))) {
                        return;
                    }

                    throw new ErrorException($message, 0, $level, $file, $line);
                }
            });
        }

        return $this;
    }
}
