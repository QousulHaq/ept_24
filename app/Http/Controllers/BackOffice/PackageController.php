<?php

namespace App\Http\Controllers\BackOffice;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Entities\Question\Package;
use App\Http\Controllers\Controller;
use App\Jobs\Package\CreateNewPackage;
use App\Jobs\Package\DeleteExistingPackage;
use App\Jobs\Package\UpdateExistingPackage;
use Inertia\Inertia;

class PackageController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     */

    // * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    
    public function index(Request $request)
    {
        // view()->share('packages', Package::query()->paginate($request->input('per_page', 15)));

        // return view('pages.package.index');

        $packages = Package::query()->paginate($request->input('per_page', 15));

        return Inertia::render('Packages/Index', compact('packages'));
        
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Entities\Question\Package $package
     */
    // * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    public function show(Request $request, Package $package)
    {
        $items = null;
        $title = null;
        $subpackage = null;
        $intros = null;

        $itemQuery = $package->items();

        if ($request->has('subpackage')) {
            $subpackage = $package->findDescendant($request->input('subpackage'));
            if (! is_null($subpackage)) {
                $itemQuery = $subpackage->items();
                $intros = $subpackage->introductions;
            }
        }

        if ($request->has('keyword')) {
            /**
             * @noinspection PhpUndefinedMethodInspection
             * @see \App\Entities\Question\Package\Item::scopeSearch
             */
            $itemQuery->search($request->input('keyword'));
        }

        $itemQuery->latest();
        
        $items = $itemQuery->paginate();
        
        $scheduledExams = $package->exams()->where('scheduled_at', '>', Carbon::now())->get();
        
        // return view('pages.package.show',
        //     compact('package', 'items', 'intros', 'subpackage', 'scheduledExams'));
        return Inertia::render('Packages/ShowPackages', [
            'package' => $package, 
            'items' => $items, 
            'intros' => $intros, 
            'subpackage' => $subpackage->fresh(['categories']), 
            'scheduledExams' => $scheduledExams,
        ]);
    }

    /**
     */
    // * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    public function create()
    {
        // return view('pages.package.create');
        return Inertia::render('Packages/Create');
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store()
    {
        $createNewPackage = new CreateNewPackage();

        $this->dispatchNow($createNewPackage);

        return ($createNewPackage->success())
            ? redirect()->route('back-office.package.index')->with('success', 'Package Created!')
            : redirect()->back()->withInput()->withErrors(['internal server error']);
    }

    /**
     * @param \App\Entities\Question\Package $package
     */
    // * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    public function edit(Package $package)
    {
        // view()->share('package', $package);

        // return view('pages.package.edit');

        return Inertia::render('Packages/Edit', compact('package'));
    }

    /**
     * @param \App\Entities\Question\Package $package
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(Package $package)
    {
        $updateExistingPackage = new UpdateExistingPackage($package);

        $this->dispatchNow($updateExistingPackage);

        return ($updateExistingPackage->success())
            ? redirect()->route('back-office.package.index')->with('success', 'Package Updated!')
            : redirect()->back()->withInput()->withErrors(['internal server error']);
    }

    /**
     * @param \App\Entities\Question\Package $package
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Package $package)
    {
        $deleteExistingPackage = new DeleteExistingPackage($package);

        $this->dispatchNow($deleteExistingPackage);

        return $deleteExistingPackage->success()
            ? redirect()->route('back-office.package.index')->with('success', 'Package Deleted!')
            : redirect()->back()->withInput()->withErrors(['internal server error']);
    }
}
