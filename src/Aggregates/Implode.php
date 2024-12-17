<?php declare(strict_types=1);

namespace Soyhuce\EloquentExtended\Aggregates;

use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Soyhuce\EloquentExtended\Aggregates\Contracts\AggregateFunction;
use Soyhuce\EloquentExtended\Aggregates\Contracts\QualifiesRelatedColumn;

/**
 * @template TModelClass of \Illuminate\Database\Eloquent\Model
 * @implements \Soyhuce\EloquentExtended\Aggregates\Contracts\AggregateFunction<TModelClass>
 */
class Implode implements AggregateFunction, QualifiesRelatedColumn
{
    private Closure $columnQualifier;

    public function __construct(
        protected string $column,
        protected string $glue,
        protected ?string $orderBy = null,
        protected ?string $direction = 'asc',
    ) {
    }

    public function column(): string
    {
        return $this->column;
    }

    public function setColumnQualifier(Closure $columnQualifier): void
    {
        $this->columnQualifier = $columnQualifier;
    }

    public function expression(string $column): string
    {
        if ($this->orderBy === null) {
            return "string_agg({$column}, ?)";
        }

        $orderBy = ($this->columnQualifier)($this->orderBy);

        return "string_agg({$column}, ? order by {$orderBy} {$this->direction})";
    }

    public function defaultAlias(string $relation, string $column): string
    {
        return $relation . ' implode ' . $column;
    }

    /**
     * @param Builder<TModelClass> $query
     */
    public function apply(Builder $query, QueryBuilder $aggregateQuery, string $alias): void
    {
        $aggregateQuery->addBinding($this->glue, 'select');

        $query->getQuery()->selectSub($aggregateQuery, $alias);
    }
}
