# Some useful extensions for Eloquent

[![Latest Version on Packagist](https://img.shields.io/packagist/v/soyhuce/laravel-eloquent-extended.svg?style=flat-square)](https://packagist.org/packages/soyhuce/laravel-eloquent-extended)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/soyhuce/laravel-eloquent-extended/run-tests?label=tests)](https://github.com/soyhuce/laravel-eloquent-extended/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/workflow/status/soyhuce/laravel-eloquent-extended/Check%20&%20fix%20styling?label=code%20style)](https://github.com/soyhuce/laravel-eloquent-extended/actions?query=workflow%3A"Check+%26+fix+styling"+branch%3Amain)
[![GitHub PHPStan Action Status](https://img.shields.io/github/workflow/status/soyhuce/laravel-eloquent-extended/PHPStan?label=phpstan)](https://github.com/soyhuce/laravel-eloquent-extended/actions?query=workflow%3APHPStan+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/soyhuce/laravel-eloquent-extended.svg?style=flat-square)](https://packagist.org/packages/soyhuce/laravel-eloquent-extended)

This is where your description should go. Limit it to a paragraph or two. Consider adding a small example.

## Installation

You can install the package via composer:

```bash
composer require soyhuce/laravel-eloquent-extended
```


## Usage

## Builder

### Misc

- Builder::scope(Illuminate\Database\Eloquent\Scope $score): Builder
- Builder::scoped(\Closure $callable): Builder

### Ordering

- Builder::preventInvalidDirection(string $direction): void
- Builder::orderByNullsLast(string $column, string $direction = 'asc'): Builder
- Builder::orderByRawNullsLast(string $sql): Builder
- Builder::orderByAggregate( string $relationName, string $column, string $direction = 'asc', ?string $function = null,
  ?Closure $constraints = null)
- Builder::orderByExists(string $relation, ?Closure $constraints = null, string $direction = 'asc')
- Builder::orderByExistsDesc(string $relation, ?Closure $constraints = null)
- Builder::orderByCount(string $relation, ?Closure $constraints = null, string $direction = 'asc')
- Builder::orderByCountDesc(string $relation, ?Closure $constraints = null)
- Builder::orderBySum(string $relation, string $column, ?Closure $constraints = null, string $direction = 'asc')
- Builder::orderBySumDesc(string $relation, string $column, ?Closure $constraints = null)
- Builder::orderByMin(string $relation, string $column, ?Closure $constraints = null, string $direction = 'asc')
- Builder::orderByMinDesc(string $relation, string $column, ?Closure $constraints = null)
- Builder::orderByMax(string $relation, string $column, ?Closure $constraints = null, string $direction = 'asc')
- Builder::orderByMaxDesc(string $relation, string $column, ?Closure $constraints = null)

### Result

- Builder::random(): Model

### Select

- Builder::withAggregateFunction( $relations, \Soyhuce\EloquentExtended\Aggregates\Contracts\AggregateFunction
  $aggregateFunction): Builder
- Builder::withAnyExists(array $relations, string $alias = 'related_exists') : Builder
- Builder::withImplode($relations, string $column, string $glue, ?string $orderBy = null, string $direction = 'asc') :
  Builder

## Eloquent Collection

- Collection::loadAttributes(Closure(Builder): Builder|void $loadWith): Collection

## Model traits

### LoadsAttributes

- Model::loadAttributes(Closure(Builder): Builder|void $loadWith): Model


## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Bastien Philippe](https://github.com/bastien-phi)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
