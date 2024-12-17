<?php declare(strict_types=1);

namespace Soyhuce\EloquentExtended\Mixins;

use Closure;
use Illuminate\Database\Query\Expression;
use Illuminate\Support\Str;
use Soyhuce\EloquentExtended\Aggregates\AnyExists;
use Soyhuce\EloquentExtended\Aggregates\Contracts\AggregateFunction;
use Soyhuce\EloquentExtended\Aggregates\Contracts\MultiRelationAggregate;
use Soyhuce\EloquentExtended\Aggregates\Contracts\QualifiesRelatedColumn;
use Soyhuce\EloquentExtended\Aggregates\Implode;
use function count;
use function is_array;

/**
 * @template TModel of \Illuminate\Database\Eloquent\Model
 * @mixin \Illuminate\Database\Eloquent\Builder<TModel>
 */
class Select
{
    public function withAggregateFunction(): Closure
    {
        /**
         * @param array<string, Closure>|array<string>|string $relations
         * @param AggregateFunction<TModel> $aggregateFunction
         */
        return function ($relations, AggregateFunction $aggregateFunction): self {
            if (empty($relations)) {
                return $this;
            }

            if ($this->getQuery()->columns === null) {
                $this->getQuery()->select([$this->getQuery()->from . '.*']);
            }

            $column = $aggregateFunction->column();

            /** @var array<string, callable> $relations */
            $relations = $this->parseWithRelations(is_array($relations) ? $relations : [$relations]);

            foreach ($relations as $name => $constraints) {
                // First we will determine if the name has been aliased using an "as" clause on the name
                // and if it has we will extract the actual relationship name and the desired name of
                // the resulting column. This allows multiple aggregates on the same relationships.
                $segments = explode(' ', $name);

                unset($alias);

                if (count($segments) === 3 && Str::lower($segments[1]) === 'as') {
                    [$name, $alias] = [$segments[0], $segments[2]];
                }

                $relation = $this->getRelationWithoutConstraints($name);

                $columnQualifier = function (string $column) use ($relation): string {
                    $hashedColumn = $this->getQuery()->from === $relation->getQuery()->getQuery()->from
                        ? "{$relation->getRelationCountHash(false)}.{$column}"
                        : $column;

                    return $this->getQuery()->getGrammar()->wrap(
                        $column === '*' ? $column : $relation->getRelated()->qualifyColumn($hashedColumn)
                    );
                };

                $wrappedColumn = $columnQualifier($column);

                if ($aggregateFunction instanceof QualifiesRelatedColumn) {
                    $aggregateFunction->setColumnQualifier($columnQualifier);
                }

                $expression = $aggregateFunction->expression($wrappedColumn);

                // Here, we will grab the relationship sub-query and prepare to add it to the main query
                // as a sub-select. First, we'll get the "has" query and use that to get the relation
                // sub-query. We'll format this relationship name and append this column if needed.
                $query = $relation->getRelationExistenceQuery(
                    $relation->getRelated()->newQuery(),
                    $this,
                    new Expression($expression)
                )->setBindings([], 'select');

                $query->callScope($constraints);

                $query = $query->mergeConstraintsFrom($relation->getQuery())->toBase();

                // If the query contains certain elements like orderings / more than one column selected
                // then we will remove those elements from the query so that it will execute properly
                // when given to the database. Otherwise, we may receive SQL errors or poor syntax.
                $query->orders = null;
                $query->setBindings([], 'order');

                if (count($query->columns) > 1) {
                    $query->columns = [$query->columns[0]];
                    $query->bindings['select'] = [];
                }

                // Finally, we will make the proper column alias to the query and run this sub-select on
                // the query builder. Then, we will return the builder instance back to the developer
                // for further constraint chaining that needs to take place on the query as needed.
                $alias ??= Str::snake(
                    preg_replace('/[^[:alnum:][:space:]_]/u', '', $aggregateFunction->defaultAlias($name, $column))
                );

                $aggregateFunction->apply($this, $query, $alias);
            }

            if ($aggregateFunction instanceof MultiRelationAggregate) {
                $aggregateFunction->enclose($this);
            }

            return $this;
        };
    }

    public function withAnyExists(): Closure
    {
        /**
         * @param array<string, Closure>|array<string> $relations
         */
        return function (array $relations, string $alias = 'related_exists'): self {
            return $this->withAggregateFunction($relations, new AnyExists($alias));
        };
    }

    public function withImplode(): Closure
    {
        /**
         * @param array<string, Closure>|array<string>|string $relations
         */
        return function (
            $relations,
            string $column,
            string $glue,
            ?string $orderBy = null,
            string $direction = 'asc',
        ): self {
            return $this->withAggregateFunction($relations, new Implode($column, $glue, $orderBy, $direction));
        };
    }
}
