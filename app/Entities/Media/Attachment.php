<?php

namespace App\Entities\Media;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Arr;
use App\Entities\Account\User;
use Illuminate\Database\Eloquent\Model;
use App\Extra\Eloquent\Concerns\UuidAsPrimaryKey;
use Illuminate\Database\Eloquent\Relations;

/**
 * Class Attachment
 * @package App\Entities\Media
 *
 * @property string id
 * @property string title
 * @property string path
 * @property-read string url
 */
class Attachment extends Model
{
    use UuidAsPrimaryKey, SoftDeletes;

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'attachments';

    /**
     * The "type" of the primary key ID.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'options' => 'array',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'title',
        'mime',
        'path',
        'type',
        'description',
        'options',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'path',
        'user_id',
        'pivot',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'url', 'order',
    ];

    /**
     * Define `belongsTo` relationship with User model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function uploader()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * Get `Url` attribute mutator.
     *
     * @return string
     */
    public function getUrlAttribute(): string
    {
        return route('api.client.attachment.show', [
            'attachment_uuid' => $this->getOriginal('id'),
        ], false);
    }

    public function attachables(): Relations\HasMany
    {
        return $this->hasMany(Attachable::class, 'attachment_id', 'id');
    }

    public function getUsedByAttribute(): int
    {
        return Attachable::query()->where('attachment_id', $this->getAttribute('id'))->count();
    }

    public function getOrderAttribute()
    {
        return Arr::get($this->getAttribute('pivot'), 'order');
    }
}
