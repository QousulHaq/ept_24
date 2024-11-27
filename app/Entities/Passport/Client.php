<?php

namespace App\Entities\Passport;

use App\Entities\Question\ClientShare;
use App\Entities\Question\Package;
use Laravel\Passport\Client as PassportClient;
use Illuminate\Database\Eloquent\Relations;

/**
 * @property  mixed id
 * @property  \Illuminate\Database\Eloquent\Collection packages
 */
class Client extends PassportClient
{
    public function skipsAuthorization()
    {
        return in_array($this->id, [env('MIX_VUE_APP_CLIENT_ID', -1), 666]);
    }

    public function packages(): Relations\BelongsToMany
    {
        return $this->belongsToMany(Package::class)
            ->withPivot(['private_key', 'public_key', 'secret', 'passphrase'])
            ->using(ClientShare::class)
            ->as('client_share');
    }
}
