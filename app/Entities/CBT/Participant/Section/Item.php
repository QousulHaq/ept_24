<?php /** @noinspection PhpMissingFieldTypeInspection */

namespace App\Entities\CBT\Participant\Section;

use App\Extra\Distribution\Encryptor;
use Illuminate\Support\Arr;
use Illuminate\Database\Eloquent\Model;
use App\Entities\CBT\Participant\Section;
use App\Extra\Contracts\HasRemainingTime;
use App\Extra\Eloquent\Concerns\TickAble;
use App\Extra\Eloquent\Concerns\Attachable;
use App\Extra\Eloquent\Concerns\HasConfigCBT;
use App\Extra\Eloquent\Scopes\RootEntityScope;
use App\Extra\Eloquent\Concerns\UuidAsPrimaryKey;
use App\Entities\Question\Package\Item as ItemReference;
use Illuminate\Database\Eloquent\Relations;

/**
 * @property-read ItemReference reference
 * @property-read \Illuminate\Database\Eloquent\Collection attempts
 * @property-read Section section
 * @property-read \Illuminate\Database\Eloquent\Collection answers
 * @property-read array tags
 * @property string label
 * @property bool is_encrypted
 * @property string encryption_id
 */
class Item extends Model implements HasRemainingTime
{
    use UuidAsPrimaryKey, Attachable, TickAble, HasConfigCBT {
        getConfigAttribute as parentGetConfigAttribute;
    }

    public $incrementing = false;

    protected $table = 'participant_section_items';

    protected $keyType = 'string';

    protected $fillable = [
        'item_id', 'section_id', 'type', 'content', 'config',
        'sub_content', 'remaining_time', 'order', 'label', 'tags',
        'is_encrypted', 'encryption_id',
    ];

    protected $appends = ['config'];

    protected $with = ['attachments', 'answers', 'attempts'];

    protected $hidden = ['reference', 'item_id', 'section_id', 'created_at', 'updated_at'];

    protected $casts = [
        'remaining_time' => 'int',
        'tags' => 'json',
        'is_encrypted' => 'bool',
    ];

    public function reference(): Relations\BelongsTo
    {
        return $this->belongsTo(ItemReference::class, 'item_id', 'id')
            ->withoutGlobalScope(RootEntityScope::class);
    }

    public function answers(): Relations\HasMany
    {
        return $this->hasMany(Item\Answer::class, 'participant_section_item_id', 'id');
    }

    public function attempts(): Relations\HasMany
    {
        return $this->hasMany(Item\Attempt::class, 'participant_section_item_id', 'id');
    }

    public function section(): Relations\BelongsTo
    {
        return $this->belongsTo(Section::class, 'section_id', 'id');
    }

    public function getConfigAttribute(): array
    {
        return Arr::get($this->parentGetConfigAttribute(),
            collect($this->tags)->values()->some('intro') ? 'intro' : 'item', []);
    }

    public function getContentAttribute(string|null $originalContent): string|null
    {
        if ($originalContent && $this->is_encrypted) {
            return $this->decryptContent($originalContent);
        }

        return $originalContent;
    }

    public function getSubContentAttribute(string|null $originalSubContent): string|null
    {
        if ($originalSubContent && $this->is_encrypted) {
            return $this->decryptContent($originalSubContent);
        }

        return $originalSubContent;
    }

    private function decryptContent(string $encryptedContent): string
    {
        if (! $encryptor = Encryptor::fromCache($this->encryption_id)) {
            return 'encrypted content, please provide dec first.';
        }

        return $encryptor->decrypt($encryptedContent);
    }
}
