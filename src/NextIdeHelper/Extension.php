<?php declare(strict_types=1);

namespace Soyhuce\EloquentExtended\NextIdeHelper;

use Illuminate\Support\Collection;
use Soyhuce\NextIdeHelper\Contracts\ModelResolver;
use Soyhuce\NextIdeHelper\Domain\Models\Entities\Model;

class Extension implements ModelResolver
{
    public function execute(Model $model): void
    {
        $modelClass = $model->fqcn;
        $builderClass = $model->queryBuilder->fqcn;

        Collection::make([
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
            ->map(fn (string $method) => " * @method {$method}")
            ->each(function (string $method) use ($model): void {
                $model->queryBuilder->addExtra($method);
            });
    }
}
