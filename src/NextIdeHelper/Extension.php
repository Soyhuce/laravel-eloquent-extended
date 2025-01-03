<?php declare(strict_types=1);

namespace Soyhuce\EloquentExtended\NextIdeHelper;

use Composer\InstalledVersions;
use Illuminate\Support\Collection;
use Soyhuce\NextIdeHelper\Contracts\ModelResolver;
use Soyhuce\NextIdeHelper\Domain\Models\Entities\Model;

class Extension implements ModelResolver
{
    public function execute(Model $model): void
    {
        $modelClass = $model->fqcn;
        $builderClass = $model->queryBuilder->fqcn;
        $collectionClass = $model->collection->fqcn;

        if ((bool) config('next-ide-helper.models.larastan_friendly', false)) {
            $collectionClass .= "<int, {$modelClass}>";
        }

        Collection::make([
            'bool insertModels(array $values)',
            'int upsertModels(array $values, string|array $uniqueBy, ?array $update = null)',
            '$this scope(\\Illuminate\\Database\\Eloquent\\Scope $scope)',
            '$this scoped(\\Closure $callable)',
            '$this preventInvalidDirection(string $direction)',
            '$this withAggregateFunction(array $relations, \\Soyhuce\\EloquentExtended\\Aggregates\\Contracts\\AggregateFunction $aggregateFunction)',
            '$this withAnyExists(array $relations, string $alias = \'related_exists\')',
            '$this withImplode(array|string $relations, string $column, string $glue, ?string $orderBy = null, string $direction = \'asc\')',
            '$this orderByNullsLast(string $column, string $direction = \'asc\')',
            '$this orderByRawNullsLast(string $sql)',
            '$this orderByNullsFirst(string $column, string $direction = \'asc\')',
            '$this orderByRawNullsFirst(string $sql)',
            '$this orderByAggregate(string $relationName, string $column, string $direction = \'asc\', ?string $function = null, ?\\Closure $constraints = null)',
            '$this orderByExists(string $relation, ?\\Closure $constraints = null, string $direction = \'asc\')',
            '$this orderByExistsDesc(string $relation, ?\\Closure $constraints = null)',
            '$this orderByCount(string $relation, ?\\Closure $constraints = null, string $direction = \'asc\')',
            '$this orderByCountDesc(string $relation, ?\\Closure $constraints = null)',
            '$this orderBySum(string $relation, string $column, ?\\Closure $constraints = null, string $direction = \'asc\')',
            '$this orderBySumDesc(string $relation, string $column, ?\\Closure $constraints = null)',
            '$this orderByMin(string $relation, string $column, ?\\Closure $constraints = null, string $direction = \'asc\')',
            '$this orderByMinDesc(string $relation, string $column, ?\\Closure $constraints = null)',
            '$this orderByMax(string $relation, string $column, ?\\Closure $constraints = null, string $direction = \'asc\')',
            '$this orderByMaxDesc(string $relation, string $column, ?\\Closure $constraints = null)',
            "{$modelClass}|null random()",
        ])
            ->when(
                InstalledVersions::isInstalled('tpetry/laravel-postgresql-enhanced'),
                fn (Collection $collection) => $collection->merge([
                    "{$collectionClass} insertModelsReturning(array \$values, array \$returning = ['*'])",
                    "{$collectionClass} upsertModelsReturning(array \$values, array|string \$uniqueBy, ?array \$update = null, array \$returning = ['*'])",
                ])
            )
            ->map(fn (string $method) => " * @method {$method}")
            ->each(function (string $method) use ($model): void {
                $model->queryBuilder->addExtra($method);
            });
    }
}
