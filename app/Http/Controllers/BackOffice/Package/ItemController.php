<?php

namespace App\Http\Controllers\BackOffice\Package;

use Illuminate\Http\Request;
use App\Entities\Question\Package;
use App\Http\Controllers\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ItemController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Entities\Question\Package $package
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Throwable
     */
    public function create(Request $request, Package $package)
    {
        $this->getPackageData($request, $package);

        return view('pages.package.item.create');
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Entities\Question\Package $package
     * @param \App\Entities\Question\Package\Item $item
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Throwable
     */
    public function edit(Request $request, Package $package, Package\Item $item)
    {
        $this->getPackageData($request, $package);

        view()->share('item', $item);
        view()->share('isIntro', view()->shared('package')->introductions()->where('id', $item->id)->exists());

        return view('pages.package.item.edit');
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Entities\Question\Package $package
     * @return void
     * @throws \Throwable
     */
    private function getPackageData(Request $request, Package $package)
    {
        view()->share('parent', $package);

        $child = null;
        if ($package->children->count() > 0) {
            $child = $package->findDescendant($request->get('subpackage', null));
            throw_if($child === null, NotFoundHttpException::class);
            view()->share('package', $package->children->count() === 0 ? $package : $child);
        } else {
            view()->share('package', $package);
        }
    }
}
