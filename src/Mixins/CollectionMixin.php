<?php declare(strict_types=1);

namespace Soyhuce\EloquentExtended\Mixins;

use Closure;
use Illuminate\Support\Arr;

/**
 * @mixin \Illuminate\Database\Eloquent\Collection
 */
class CollectionMixin
{
    public function loadAttributes(): Closure
    {
        return function (Closure $closure): self {
            if ($this->isEmpty()) {
                return $this;
            }

            $query = $this->first()->newModelQuery()
                ->whereKey($this->modelKeys())
                ->select($this->first()->getKeyName());
            $query = $closure($query) ?? $query;
            $models = $query->get()->keyBy($this->first()->getKeyName());

            $attributes = Arr::except(
                array_keys($models->first()->getAttributes()),
                $models->first()->getKeyName()
            );

            $this->each(function ($model) use ($models, $attributes): void {
                $fetchedModel = $models->get($model->getKey());
                $extraAttributes = Arr::only($fetchedModel->getAttributes(), $attributes);

                $model->forceFill($extraAttributes)
                    ->syncOriginalAttributes($attributes)
                    ->mergeCasts($fetchedModel->getCasts());
            });

            return $this;
        };
    }
}
