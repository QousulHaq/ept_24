<?php

namespace App\Http\Controllers\Api\BackOffice;

use Illuminate\Http\Request;
use App\Entities\Classification;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Contracts\Support\Responsable;

class ClassificationController extends Controller
{
    public function index(Request $request)
    {
        return Classification::query()
            ->when($request->has('type'), fn (Builder $builder) => $builder->where('type', $request->input('type')))
            ->get();
    }

    public function store(): Responsable
    {
        // TODO : part of #13
    }

    public function update(Classification $classification): Responsable
    {
        // TODO : part of #13
    }

    public function destroy(Classification $classification): Responsable
    {
        // TODO : part of #13
    }
}
