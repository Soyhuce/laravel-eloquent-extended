<?php declare(strict_types=1);

namespace Soyhuce\EloquentExtended\Aggregates;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Collection;
use Soyhuce\EloquentExtended\Aggregates\Contracts\MultiRelationAggregate;

/**
 * @template TModelClass of \Illuminate\Database\Eloquent\Model
 * @implements \Soyhuce\EloquentExtended\Aggregates\Contracts\MultiRelationAggregate<TModelClass>
 */
class AnyExists implements MultiRelationAggregate
{
    /** @var \Illuminate\Support\Collection<int, \Illuminate\Database\Query\Builder> */
    protected Collection $existenceQueries;

    public function __construct(
        protected string $alias = 'related_exists',
    ) {
        $this->existenceQueries = new Collection();
    }

    public function column(): string
    {
        return '*';
    }

    public function expression(string $column): string
    {
        return $column;
    }

    /**
     * @phpcsSuppress SlevomatCodingStandard.Functions.UnusedParameter
     */
    public function defaultAlias(string $relation, string $column): string
    {
        return $this->alias;
    }

    /**
     * @phpcsSuppress SlevomatCodingStandard.Functions.UnusedParameter
     * @param \Illuminate\Database\Eloquent\Builder<TModelClass> $query
     */
    public function apply(Builder $query, QueryBuilder $aggregateQuery, string $alias): void
    {
        $this->existenceQueries->add($aggregateQuery);
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder<TModelClass> $query
     */
    public function enclose(Builder $query): void
    {
        $query
            ->getQuery()
            ->selectRaw(
                sprintf(
                    '%s as %s',
                    $this->existenceQueries
                        ->map(fn (QueryBuilder $query) => sprintf('exists(%s)', $query->toSql()))
                        ->implode(' or '),
                    $query->getQuery()->grammar->wrap($this->alias)
                ),
                $this->existenceQueries
                    ->flatMap(fn (QueryBuilder $query) => $query->getBindings())
                    ->all()
            );
        $query->withCasts([$this->alias => 'bool']);
    }
}
