<?php declare(strict_types=1);

namespace Soyhuce\EloquentExtended\Aggregates\Contracts;

use Closure;

interface QualifiesRelatedColumn
{
    public function setColumnQualifier(Closure $columnQualifier): void;
}
