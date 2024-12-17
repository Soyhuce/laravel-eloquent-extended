<?php declare(strict_types=1);

namespace Soyhuce\EloquentExtended\Mixins;

use Closure;
use Illuminate\Support\Arr;

/**
 * @template TKey of array-key
 * @template TModel of \Illuminate\Database\Eloquent\Model
 * @mixin \Illuminate\Database\Eloquent\Collection<TKey, TModel>
 */
class CollectionMixin
{
    public function loadAttributes(): Closure
    {
        return function (Closure $closure): self {
            $model = $this->first();
            if ($model === null) {
                return $this;
            }

            $query = $model->newModelQuery()
                ->whereKey($this->modelKeys())
                ->select($model->getKeyName());
            $query = $closure($query) ?? $query;
            $models = $query->get()->keyBy($model->getKeyName());

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
