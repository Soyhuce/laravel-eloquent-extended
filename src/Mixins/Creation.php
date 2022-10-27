<?php declare(strict_types=1);

namespace Soyhuce\EloquentExtended\Mixins;

use Closure;
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
}
