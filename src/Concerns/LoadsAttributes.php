<?php declare(strict_types=1);

namespace Soyhuce\EloquentExtended\Concerns;

use Closure;

trait LoadsAttributes
{
    /**
     * return $this.
     */
    public function loadAttributes(Closure $closure)
    {
        $this->newCollection([$this])->loadAttributes($closure);

        return $this;
    }
}
