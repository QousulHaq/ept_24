<?php

namespace App\Http\Controllers\Api\BackOffice;

use App\Extra\Eloquent\Scopes\RootEntityScope;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Entities\Question\Package;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ItemController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $attributes = $request->validate([
            'package_id' => ['required', Rule::exists('packages', 'id')],
            'query' => ['nullable'],
        ]);

        /** @var Package $package */
        $package = Package::query()->withoutGlobalScope(RootEntityScope::class)->find($attributes['package_id']);

        $query = Package\Item::query();

        $query->whereDoesntHave('packages',
            fn(Builder $packageQuery) => $packageQuery->where('packages.id', '==', $package->id));

        $query->whereHas('packages',
            fn (Builder $packagesQuery) => $packagesQuery
                ->where('config', $package->getRawOriginal('config'))
                ->withoutGlobalScope(RootEntityScope::class)
        );

        $query->when($attributes['query'] ?? false, fn ($packageQuery, $value) => $packageQuery->search($value));

        $payload = $query->cursorPaginate($request->input('per_page'))->withQueryString();

        collect($payload->items())->each(fn(Package\Item $item) => $item->append('category_name'));

        return response()->json($payload);
    }

    public function attach(Package $package, Request $request): JsonResponse
    {
        $attributes = $request->validate([
            'item_id' => ['required', Rule::exists('items', 'id')],
        ]);

        if ($package->items()->where('id', $attributes['item_id'])->exists()) {
            throw ValidationException::withMessages([
                'already attached!',
            ]);
        }

        $package->items()->attach($attributes['item_id']);

        return response()->json($package);
    }
}
