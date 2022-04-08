<?php declare(strict_types=1);

namespace Soyhuce\EloquentExtended\Aggregates\Contracts;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;

/**
 * @template TModelClass of \Illuminate\Database\Eloquent\Model
 */
interface AggregateFunction
{
    /**
     * Column on to aggregate.
     */
    public function column(): string;

    /**
     * Define the aggregate expression.
     */
    public function expression(string $column): string;

    /**
     * The columns alias used by default if it is not provided by the user.
     */
    public function defaultAlias(string $relation, string $column): string;

    /**
     * Apply the aggregateQuery in the query.
     * The method is executed on each relation.
     *
     * @param \Illuminate\Database\Eloquent\Builder<TModelClass> $query
     */
    public function apply(Builder $query, QueryBuilder $aggregateQuery, string $alias): void;
}
