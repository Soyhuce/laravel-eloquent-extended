<?php declare(strict_types=1);

namespace Soyhuce\EloquentExtended;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider;
use Soyhuce\EloquentExtended\Mixins\CollectionMixin;
use Soyhuce\EloquentExtended\Mixins\Creation;
use Soyhuce\EloquentExtended\Mixins\Misc;
use Soyhuce\EloquentExtended\Mixins\Ordering;
use Soyhuce\EloquentExtended\Mixins\Result;
use Soyhuce\EloquentExtended\Mixins\Select;

class EloquentExtendedServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Builder::mixin(new Creation());
        Builder::mixin(new Misc());
        Builder::mixin(new Ordering());
        Builder::mixin(new Result());
        Builder::mixin(new Select());

        Collection::mixin(new CollectionMixin());
    }
}
