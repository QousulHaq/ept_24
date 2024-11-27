<?php

namespace App\Entities\Question;

use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * Class ClientShare
 * @package App\Entities\Question
 *
 * @property string public_key
 * @property string private_key
 * @property string secret
 * @property string passphrase
 */
class ClientShare extends Pivot
{
    protected $table = 'client_package';

    protected $hidden = ['private_key', 'client_id', 'package_id', 'secret', 'passphrase'];

    protected $casts = [
        'private_key' => 'encrypted',
        'public_key' => 'encrypted',
        'secret' => 'encrypted',
        'passphrase' => 'encrypted',
        'last_sync' => 'datetime',
    ];
}
