<?php declare(strict_types=1);

namespace Soyhuce\EloquentExtended\Mixins;

use Closure;
use Illuminate\Database\Eloquent\Scope;

/**
 * @template TModel of \Illuminate\Database\Eloquent\Model
 * @mixin \Illuminate\Database\Eloquent\Builder<TModel>
 */
class Misc
{
    public function scope(): Closure
    {
        return function (Scope $scope): self {
            $scope->apply($this, $this->getModel());

            return $this;
        };
    }

    public function scoped(): Closure
    {
        return function (Closure $callback): self {
            $result = $callback($this);

            return $result ?? $this;
        };
    }
}
