<?php declare(strict_types=1);

namespace Soyhuce\EloquentExtended\Mixins;

use Closure;
use Composer\InstalledVersions;
use Illuminate\Database\Eloquent\Collection;
use LogicException;
use function is_array;

/**
 * @template TModel of \Illuminate\Database\Eloquent\Model
 * @mixin \Illuminate\Database\Eloquent\Builder<TModel>
 */
class Creation
{
    public function insertModels(): Closure
    {
        return function (array $values): bool {
            if (empty($values)) {
                return true;
            }

            if (!is_array(reset($values))) {
                $values = [$values];
            }

            $values = array_map(
                fn (array $value): array => $this->newModelInstance($value)->getAttributes(),
                $values
            );

            return $this->toBase()->insert(
                $this->addTimestampsToUpsertValues($values)
            );
        };
    }

    public function upsertModels(): Closure
    {
        return function (array $values, string|array $uniqueBy, ?array $update = null): int {
            if (empty($values)) {
                return 0;
            }

            if (!is_array(reset($values))) {
                $values = [$values];
            }

            $values = array_map(
                fn (array $value): array => $this->newModelInstance($value)->getAttributes(),
                $values
            );

            return $this->upsert($values, $uniqueBy, $update);
        };
    }

    public function upsertModelsReturning(): Closure
    {
        return function (array $values, array|string $uniqueBy, ?array $update = null, array $returning = ['*']): Collection {
            if (!InstalledVersions::isInstalled('tpetry/laravel-postgresql-enhanced')) {
                throw new LogicException('You must install tpetry/laravel-postgresql-enhanced to use upsertModelsReturning');
            }

            if (empty($values)) {
                return $this->newModelInstance()->newCollection();
            }

            if (!is_array(reset($values))) {
                $values = [$values];
            }

            $values = array_map(
                fn (array $value): array => $this->newModelInstance($value)->getAttributes(),
                $values
            );

            return $this->upsertReturning($values, $uniqueBy, $update, $returning);
        };
    }
}
