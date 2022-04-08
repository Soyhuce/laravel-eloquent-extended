<?php declare(strict_types=1);

namespace Soyhuce\EloquentExtended\Tests\Fixtures;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Soyhuce\EloquentExtended\Concerns\LoadsAttributes;

class User extends Model
{
    use HasFactory;
    use LoadsAttributes;

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    public function publishedPosts(): HasMany
    {
        return $this->hasMany(Post::class)->where('published', true);
    }
}
