<?php declare(strict_types=1);

namespace Soyhuce\EloquentExtended\Aggregates\Contracts;

use Illuminate\Database\Eloquent\Builder;

/**
 * @template TModelClass of \Illuminate\Database\Eloquent\Model
 * @extends \Soyhuce\EloquentExtended\Aggregates\Contracts\AggregateFunction<TModelClass>
 */
interface MultiRelationAggregate extends AggregateFunction
{
    /**
     * Enclose the aggregate on the query.
     * This method is executed once for all relations.
     *
     * @param Builder<TModelClass> $query
     */
    public function enclose(Builder $query): void;
}
