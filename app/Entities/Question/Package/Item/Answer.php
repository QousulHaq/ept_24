<?php

namespace App\Entities\Question\Package\Item;

use App\Entities\Question\Package\Item;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use App\Extra\Eloquent\Concerns\UuidAsPrimaryKey;

class Answer extends Model
{
    use UuidAsPrimaryKey, HasFactory;

    public $incrementing = false;

    protected $table = 'item_answers';

    protected $keyType = 'string';

    protected $fillable = ['order', 'correct_answer', 'content'];

    protected $hidden = ['correct_answer'];

    protected $touches = ['item'];

    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id', 'id');
    }

    protected static function booted()
    {
        parent::booted();
        self::addGlobalScope(fn (Builder $builder) => $builder->orderBy('order'));
    }
}
