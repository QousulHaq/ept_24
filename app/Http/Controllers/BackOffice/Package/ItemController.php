<?php

namespace App\Http\Controllers\BackOffice\Package;

use Illuminate\Http\Request;
use App\Entities\Question\Package;
use App\Http\Controllers\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Inertia\Inertia;
use App\Http\Requests\Package\Item\UpdateItemRequest;
use App\Http\Requests\Package\Item\StoreItemRequest;
use App\Jobs\Package\Item\CreateNewItem;
use App\Jobs\Package\Item\UpdateExistingItem;

class ItemController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Entities\Question\Package $package
     * @throws \Throwable
     */
    // * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    public function create(Request $request, Package $package)
    {
        // $this->getPackageData($request, $package);
        // return view('pages.package.item.create');
        
        $subpackageId = request()->query('subpackage');

        $subpackage = $package->findDescendant($subpackageId);

        // Lakukan sesuatu dengan data Subpackage dan kategorinya
        return Inertia::render("Packages/Item/Create",[
            'package_id' => $package->id,
            'subpackage_id' => $subpackage->id,
            'config' => $subpackage->config,
            'categories' => $subpackage->fresh(['categories'])->categories,
        ]);
    }

    public function store(StoreItemRequest $request, Package $package)
    {
        $subpackageId = request()->query('subpackage');
        $subpackage = $package->findDescendant($subpackageId);
    
        $job = new CreateNewItem($request, $subpackage);
        $this->dispatchNow($job);

        return ($job->success())
            ? redirect()->route('back-office.package.index')->with('success', 'Item Updated!')
            : redirect()->back()->withInput()->withErrors(['internal server error']);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Entities\Question\Package $package
     * @param \App\Entities\Question\Package\Item $item
     * @throws \Throwable
     */
    // * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    public function edit(Request $request, Package $package, Package\Item $item)
    {
        $this->getPackageData($request, $package);

        // view()->share('item', $item);
        $isIntro = view()->share('isIntro', view()->shared('package')->introductions()->where('id', $item->id)->exists());

        $item->answers->each(fn (Package\Item\Answer $answer) => $answer->makeVisible('correct_answer'));
        $item->children->each(function (Package\Item $item) {
            $item->answers->each(fn (Package\Item\Answer $answer) => $answer->makeVisible('correct_answer'));
        });

        $subpackageId = request()->query('subpackage');

        $subpackage = $package->findDescendant($subpackageId);

        // return view('pages.package.item.edit');
        return Inertia::render("Packages/Item/Edit",[
            'item' => $item,
            'isIntro' => $isIntro,
            'package_id' => $package->id,
            'config' => $subpackage->config,
            'categories' => $subpackage->fresh(['categories'])->categories,
        ]);
    }

    public function update(UpdateItemRequest $request, Package $package, Package\Item $item)
    {
        $job = new UpdateExistingItem($request, $item, $package);
        $this->dispatchNow($job);

        return ($job->success())
            ? redirect()->route('back-office.package.index')->with('success', 'Item Updated!')
            : redirect()->back()->withInput()->withErrors(['internal server error']);
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
