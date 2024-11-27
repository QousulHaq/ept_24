<?php

namespace App\Http\Controllers\Api\BackOffice;

use App\Entities\Account\User;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function participant(Request $request): JsonResponse
    {
        $query = User::whereIs('student');
        $query->when($request->input('keyword', null), fn (Builder $builder, $value) => $builder->search($value));
        $query->latest();

        return response()->json($query->paginate());
    }
}
