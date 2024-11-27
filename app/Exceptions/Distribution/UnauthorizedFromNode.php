<?php

namespace App\Exceptions\Distribution;

use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\Response;

class UnauthorizedFromNode extends \Exception implements Responsable
{
    public function toResponse($request)
    {
        return response()->json(data: [
            'message' => 'credentials didn\'t match for given host.',
        ], status: Response::HTTP_FORBIDDEN);
    }
}
