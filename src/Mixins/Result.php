<?php declare(strict_types=1);

namespace Soyhuce\EloquentExtended\Mixins;

use Closure;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Result
{
    public function random(): Closure
    {
        return function (): ?Model {
            $total = $this->toBase()->getCountForPagination();

            if ($total === 0) {
                return null;
            }

            return $this->offset(mt_rand(0, $total - 1))->first();
        };
    }
}
