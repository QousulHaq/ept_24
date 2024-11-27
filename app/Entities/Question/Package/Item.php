<?php

namespace App\Entities\Question\Package;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use OwenIt\Auditing\Auditable;
use App\Entities\Question\Package;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use App\Extra\Eloquent\Concerns\Attachable;
use App\Extra\Eloquent\Concerns\Classifiable;
use App\Extra\Eloquent\Concerns\UuidAsPrimaryKey;
use App\Extra\Eloquent\Concerns\SelfRelationEntity;
use Illuminate\Database\Eloquent\Relations\HasMany;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use Illuminate\Database\Eloquent\Relations;

/**
 * @property-read \Illuminate\Database\Eloquent\Collection answers
 * @property string id
 * @property string code
 */
class Item extends Model implements AuditableContract
{
    use UuidAsPrimaryKey, Attachable, Classifiable, Auditable, HasFactory, SelfRelationEntity {
        children as traitChildren;
    }

    public const TYPE_SIMPLE = 'simple';
    public const TYPE_MULTI_CHOICE_SINGLE = 'multi_choice_single';
    public const TYPE_BUNDLE = 'bundle';
    public const TYPE_MULTI_CHOICE = 'multi_choice';
    public const TYPE_ANSWER_IN_QUESTION = 'answer_in_question';
    public const TYPE_FILL_IN_BLANK = 'fill_in_blank';
    public const TYPE_ESSAY = 'essay';
    public const TYPE_TRUE_FALSE = 'true_false';

    public $incrementing = false;

    protected $table = 'items';

    protected $keyType = 'string';

    protected $fillable = [
        'parent_id', 'code', 'type', 'content',
        'answer_order_random', 'duration', 'item_count', 'order',
    ];

    protected $with = ['children', 'answers'];

    protected $touches = ['packages', 'parent'];

    protected $appends = ['category', 'attachment'];

    public function packages(): Relations\BelongsToMany
    {
        return $this->belongsToMany(Package::class, 'package_item', 'item_id', 'package_id')
            ->withPivot(['score', 'order', 'type'])
            ->as('detail');
    }

    public function answers(): HasMany
    {
        return $this->hasMany(Item\Answer::class, 'item_id', 'id');
    }

    public function getCategoryAttribute()
    {
        return Arr::get($this->getAttribute('classification'), 'hash');
    }

    public function getCategoryNameAttribute()
    {
        return Arr::get($this->getAttribute('classification'), 'name');
    }

    public function children(): HasMany
    {
        return $this->traitChildren()->orderBy('order');
    }

    public function scopeSearch(Builder $builder, $keyword): void
    {
        $builder->where(function (Builder $builder) use ($keyword) {
            $builder->where('content', 'like', "%{$keyword}%")
                ->orWhere('code', 'like', "%{$keyword}%")
                ->orWhereHas('answers', function (Builder $builder) use ($keyword) {
                    $builder->where('content', 'like', "%{$keyword}}");
                })->orWhereHas('children', function (Builder $builder) use ($keyword) {
                    $builder->where('content', 'like', "%{$keyword}}")
                        ->orWhere('code', 'like', "%{$keyword}%")
                        ->orWhereHas('answers', function (Builder $builder) use ($keyword) {
                            $builder->where('content', 'like', "%{$keyword}}");
                        });
                });
        });
    }

    public static function getAvailableTypes(): array
    {
        $class = new \ReflectionClass(__CLASS__);

        return array_values(Arr::where($class->getConstants(), fn ($_, $key) => Str::startsWith($key, 'TYPE')));
    }
}
