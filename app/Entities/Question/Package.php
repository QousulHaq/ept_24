<?php /** @noinspection PhpMissingFieldTypeInspection */

namespace App\Entities\Question;

use App\Entities\CBT\Exam;
use App\Entities\Passport\Client;
use App\Extra\Contracts\Preset;
use App\Entities\Classification;
use App\Entities\Media\Attachment;
use App\Extra\Distribution;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Extra\Eloquent\Concerns\Classifiable;
use App\Extra\Eloquent\Concerns\HasConfigCBT;
use App\Extra\Eloquent\Concerns\GlobalQueryOrder;
use App\Extra\Eloquent\Concerns\UuidAsPrimaryKey;
use App\Extra\Eloquent\Concerns\SelfRelationEntity;
use Illuminate\Database\Eloquent\Relations;

/**
 * Class Package.
 *
 * @property-read Preset $preset
 * @property-read \Illuminate\Database\Eloquent\Collection $items
 * @property-read \Illuminate\Database\Eloquent\Collection $categories
 * @property-read \Illuminate\Database\Eloquent\Collection introductions
 * @property-read string title
 * @property mixed id
 * @property ClientShare client_share
 * @property bool is_encrypted
 * @property array distribution_options
 * @property-read Distribution\Decorator distribution
 */
class Package extends Model
{
    use UuidAsPrimaryKey, SelfRelationEntity, Classifiable, GlobalQueryOrder, HasConfigCBT, HasFactory;

    public const PACKAGE_ITEM_TYPE_INTRO = 'intro';
    public const PACKAGE_ITEM_TYPE_QUESTION = 'question';

    public $incrementing = false;

    protected $table = 'packages';

    protected $keyType = 'string';

    protected $fillable = [
        'title', 'code', 'description', 'level',
        'parent_id', 'depth', 'duration', 'max_score',
        'random_item', 'item_duration', 'config', 'note',
    ];

    protected $with = [
        'children',
    ];

    protected $touches = ['parent'];

    protected $casts = [
        'note' => 'array',
        'is_encrypted' => 'bool',
        'distribution_options' => 'array',
    ];

    public function attachments(): Relations\MorphToMany
    {
        return $this->morphToMany(Attachment::class, 'attachable', 'attachable');
    }

    public function introductions(): Relations\BelongsToMany
    {
        return $this->belongsToMany(Package\Item::class, 'package_item', 'package_id', 'item_id')
            ->wherePivot('type', self::PACKAGE_ITEM_TYPE_INTRO)
            ->as('intro');
    }

    public function items(): Relations\BelongsToMany
    {
        return $this->belongsToMany(Package\Item::class, 'package_item', 'package_id', 'item_id')
            ->wherePivot('type', self::PACKAGE_ITEM_TYPE_QUESTION)
            ->withPivot(['score', 'order', 'type'])
            ->as('detail');
    }

    public function allItems(): Relations\BelongsToMany
    {
        return $this->belongsToMany(Package\Item::class, 'package_item', 'package_id', 'item_id')
            ->withPivot(['score', 'order', 'type'])
            ->as('detail');
    }

    public function categories(): Relations\MorphToMany
    {
        return $this->classifications()->where('type', Classification::TYPE_CATEGORY);
    }

    public function exams(): Relations\HasMany
    {
        return $this->hasMany(Exam::class, 'package_id', 'id');
    }

    public function clients(): Relations\BelongsToMany
    {
        return $this->belongsToMany(Client::class)
            ->withPivot(['private_key', 'public_key', 'secret', 'passphrase'])
            ->using(ClientShare::class)
            ->as('client_share');
    }

    /**
     * @throws \Spatie\Crypto\Rsa\Exceptions\CouldNotDecryptData
     * @throws \App\Exceptions\Distribution\FailedDecryptSecret
     */
    public function getDistributionAttribute(): Distribution\Decorator
    {
        /** @var Distribution $distribution */
        $distribution = app(Distribution::class);

        return $distribution->from($this);
    }

    public function getDistributionOptionsAttribute(string|null $originalDistributionOption): array|null
    {
        try {
            $distributionOption = json_decode($originalDistributionOption, true, 512, JSON_THROW_ON_ERROR);

            $distributionOption['encryptor_ready'] = Distribution\Encryptor::cacheExists($distributionOption['package_id']);

            return $distributionOption;
        } catch (\JsonException) {
            return null;
        }
    }
}
