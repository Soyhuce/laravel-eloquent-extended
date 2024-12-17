<?php declare(strict_types=1);

namespace Soyhuce\EloquentExtended\Mixins;

use Closure;
use Illuminate\Database\Query\Expression;
use InvalidArgumentException;
use function count;
use function in_array;
use function sprintf;

/**
 * @template TModel of \Illuminate\Database\Eloquent\Model
 * @mixin \Illuminate\Database\Eloquent\Builder<TModel>
 */
class Ordering
{
    public function preventInvalidDirection(): Closure
    {
        return function (string $direction): void {
            if (!in_array($direction, ['asc', 'desc'], true)) {
                throw new InvalidArgumentException('Order direction must be "asc" or "desc".');
            }
        };
    }

    public function orderByNullsLast(): Closure
    {
        return function (string $column, string $direction = 'asc'): self {
            $this->preventInvalidDirection($direction);

            $column = $this->getGrammar()->wrap($column);

            $this->orderByRaw("{$column} {$direction} nulls last");

            return $this;
        };
    }

    public function orderByRawNullsLast(): Closure
    {
        return function (string $sql): self {
            $this->orderByRaw("{$sql} nulls last");

            return $this;
        };
    }

    public function orderByNullsFirst(): Closure
    {
        return function (string $column, string $direction = 'asc'): self {
            $this->preventInvalidDirection($direction);

            $column = $this->getGrammar()->wrap($column);

            $this->orderByRaw("{$column} {$direction} nulls first");

            return $this;
        };
    }

    public function orderByRawNullsFirst(): Closure
    {
        return function (string $sql): self {
            $this->orderByRaw("{$sql} nulls first");

            return $this;
        };
    }

    public function orderByAggregate(): Closure
    {
        return function (
            string $relationName,
            string $column,
            string $direction = 'asc',
            ?string $function = null,
            ?Closure $constraints = null,
        ) {
            $this->preventInvalidDirection($direction);

            if ($constraints === null) {
                $constraints = static function (): void {
                };
            }

            $relation = $this->getRelationWithoutConstraints($relationName);

            if ($function) {
                $hashedColumn = $this->getQuery()->from === $relation->getQuery()->getQuery()->from
                    ? "{$relation->getRelationCountHash(false)}.{$column}"
                    : $column;

                $wrappedColumn = $this->getQuery()->getGrammar()->wrap(
                    $column === '*' ? $column : $relation->getRelated()->qualifyColumn($hashedColumn)
                );

                $expression = $function === 'exists' ? $wrappedColumn : sprintf('%s(%s)', $function, $wrappedColumn);
            } else {
                $expression = $column;
            }

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

            if ($function === 'exists') {
                $this->orderByRaw(
                    sprintf('exists(%s) %s', $query->toSql(), $direction),
                    $query->getBindings()
                );
            } else {
                $this->orderBy($function ? $query : $query->limit(1), $direction);
            }

            return $this;
        };
    }

    public function orderByExists(): Closure
    {
        return function (string $relation, ?Closure $constraints = null, string $direction = 'asc') {
            return $this->orderByAggregate($relation, '*', $direction, 'exists', $constraints);
        };
    }

    public function orderByExistsDesc(): Closure
    {
        return function (string $relation, ?Closure $constraints = null) {
            return $this->orderByExists($relation, $constraints, 'desc');
        };
    }

    public function orderByCount(): Closure
    {
        return function (string $relation, ?Closure $constraints = null, string $direction = 'asc') {
            return $this->orderByAggregate($relation, '*', $direction, 'count', $constraints);
        };
    }

    public function orderByCountDesc(): Closure
    {
        return function (string $relation, ?Closure $constraints = null) {
            return $this->orderByCount($relation, $constraints, 'desc');
        };
    }

    public function orderBySum(): Closure
    {
        return function (string $relation, string $column, ?Closure $constraints = null, string $direction = 'asc') {
            return $this->orderByAggregate($relation, $column, $direction, 'sum', $constraints);
        };
    }

    public function orderBySumDesc(): Closure
    {
        return function (string $relation, string $column, ?Closure $constraints = null) {
            return $this->orderBySum($relation, $column, $constraints, 'desc');
        };
    }

    public function orderByMin(): Closure
    {
        return function (string $relation, string $column, ?Closure $constraints = null, string $direction = 'asc') {
            return $this->orderByAggregate($relation, $column, $direction, 'min', $constraints);
        };
    }

    public function orderByMinDesc(): Closure
    {
        return function (string $relation, string $column, ?Closure $constraints = null) {
            return $this->orderByMin($relation, $column, $constraints, 'desc');
        };
    }

    public function orderByMax(): Closure
    {
        return function (string $relation, string $column, ?Closure $constraints = null, string $direction = 'asc') {
            return $this->orderByAggregate($relation, $column, $direction, 'max', $constraints);
        };
    }

    public function orderByMaxDesc(): Closure
    {
        return function (string $relation, string $column, ?Closure $constraints = null) {
            return $this->orderByMax($relation, $column, $constraints, 'desc');
        };
    }
}
