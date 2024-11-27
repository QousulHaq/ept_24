<?php

namespace App\Entities\CBT\Participant\Section\Item;

use App\Extra\Distribution\Encryptor;
use Illuminate\Database\Eloquent\Model;
use App\Entities\CBT\Participant\Section\Item;
use App\Extra\Eloquent\Concerns\UuidAsPrimaryKey;
use Illuminate\Database\Eloquent\Relations;

/**
 * @property bool correct_answer
 * @property bool is_encrypted
 * @property string encryption_id
 */
class Answer extends Model
{
    use UuidAsPrimaryKey;

    public $incrementing = false;

    protected $table = 'participant_section_item_answers';

    protected $keyType = 'string';

    protected $fillable = ['participant_item_id', 'content', 'correct_answer', 'order', 'is_encrypted', 'encryption_id'];

    protected $hidden = ['correct_answer', 'created_at', 'updated_at', 'participant_section_item_id'];

    protected $casts = ['order' => 'int', 'correct_answer' => 'bool'];

    public function item(): Relations\BelongsTo
    {
        return $this->belongsTo(Item::class, 'participant_item_id', 'id');
    }

    public function getContentAttribute(string $originalContent): string
    {
        if ($this->is_encrypted) {
            if (! $encryptor = Encryptor::fromCache($this->encryption_id)) {
                return 'encrypted content, please provide dec first.';
            }

            return $encryptor->decrypt($originalContent);
        }

        return $originalContent;
    }
}
