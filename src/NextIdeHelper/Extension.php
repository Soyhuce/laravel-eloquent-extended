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
            "{$builderClass} scope(\\Illuminate\\Database\\Eloquent\\Scope \$scope)",
            "{$builderClass} scoped(\\Closure \$callable)",
            "{$builderClass} preventInvalidDirection(string \$direction)",
            "{$builderClass} withAggregateFunction(array \$relations, \\Soyhuce\\EloquentExtended\\Aggregates\\Contracts\\AggregateFunction \$aggregateFunction)",
            "{$builderClass} withAnyExists(array \$relations, string \$alias = 'related_exists')",
            "{$builderClass} withImplode(array|string \$relations, string \$column, string \$glue, ?string \$orderBy = null, string \$direction = 'asc')",
            "{$builderClass} orderByNullsLast(string \$column, string \$direction = 'asc')",
            "{$builderClass} orderByRawNullsLast(string \$sql)",
            "{$builderClass} orderByAggregate(string \$relationName, string \$column, string \$direction = 'asc', ?string \$function = null, ?\\Closure \$constraints = null)",
            "{$builderClass} orderByExists(string \$relation, ?\\Closure \$constraints = null, string \$direction = 'asc')",
            "{$builderClass} orderByExistsDesc(string \$relation, ?\\Closure \$constraints = null)",
            "{$builderClass} orderByCount(string \$relation, ?\\Closure \$constraints = null, string \$direction = 'asc')",
            "{$builderClass} orderByCountDesc(string \$relation, ?\\Closure \$constraints = null)",
            "{$builderClass} orderBySum(string \$relation, string \$column, ?\\Closure \$constraints = null, string \$direction = 'asc')",
            "{$builderClass} orderBySumDesc(string \$relation, string \$column, ?\\Closure \$constraints = null)",
            "{$builderClass} orderByMin(string \$relation, string \$column, ?\\Closure \$constraints = null, string \$direction = 'asc')",
            "{$builderClass} orderByMinDesc(string \$relation, string \$column, ?\\Closure \$constraints = null)",
            "{$builderClass} orderByMax(string \$relation, string \$column, ?\\Closure \$constraints = null, string \$direction = 'asc')",
            "{$builderClass} orderByMaxDesc(string \$relation, string \$column, ?\\Closure \$constraints = null)",
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
