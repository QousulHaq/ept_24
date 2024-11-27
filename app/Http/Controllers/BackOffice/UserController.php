<?php

namespace App\Http\Controllers\BackOffice;

use App\Actions\User\ImportUserFromCsv;
use App\Entities\Media\Attachment;
use App\Jobs\User\DeleteExistingUser;
use Closure;
use Illuminate\Contracts\View\Factory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Entities\Account\User;
use App\Jobs\User\CreateNewUser;
use Illuminate\View\View;
use Silber\Bouncer\Database\Role;
use App\Http\Controllers\Controller;
use App\Jobs\User\UpdateExistingUser;
use function view;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware(function (Request $request, Closure $next) {
            $query = Role::query();
            $query->where('name', '=', $request->route('role'));

            if ($query->count() < 1) {
                abort(404);
            }

            return $next($request);
        });
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @param $role
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @noinspection PhpUndefinedMethodInspection
     */
    public function index(Request $request, $role): Factory|View
    {
        view()->share('role', $role);

        $query = User::whereIs($role);
        $query->when($request->input('keyword', null), fn (Builder $builder, $value) => $builder->search($value));
        $query->latest();
        view()->share('users', $query->paginate($request->input('per_page', 15)));

        return view('pages.user.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param $role
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create($role): Factory|View
    {
        view()->share('role', $role);
        view()->share('available_roles',
            Role::query()->where('id', '>=', $this->authenticatedUser()->roles->min->id)->get());

        return view('pages.user.create');
    }

    public function import(string $role): Factory|View
    {
        view()->share('role', $role);
        view()->share('available_roles',
            Role::query()->where('id', '>=', $this->authenticatedUser()->roles->min->id)->get());
        view()->share('example_link', Attachment::query()->where('title', 'user_example_import')->first()?->url);

        return view('pages.user.import');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @param $role
     * @return RedirectResponse
     * @noinspection PhpUnusedParameterInspection
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request, $role): RedirectResponse
    {
        $createNewUser = new CreateNewUser($request->all());

        $this->dispatchNow($createNewUser);

        return redirect()->route('back-office.user.index', $request->route('role'))->with('success', 'User Created!');
    }

    /**
     * @throws \League\Csv\Exception
     */
    public function storeImport(ImportUserFromCsv $action, $role): RedirectResponse
    {
        $action->import();

        return redirect()->route('back-office.user.index', $role)->with('success', 'User Imported!');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param $role
     * @param User $user
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($role, User $user): Factory|View
    {
        view()->share('role', $role);
        view()->share('user', $user);
        view()->share('available_roles',
            Role::query()->where('id', '>=', $this->authenticatedUser()->roles->min->id)->get());
        view()->share('image_user', $user->attachments()->first());

        return view('pages.user.edit');
    }

    /**
     * Show the form for editing password.
     *
     * @param $role
     * @param User $user
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function editPassword($role, User $user): Factory|View
    {
        abort_if($this->authenticatedUser()->isNotA('superuser'), 403);

        view()->share('role', $role);
        view()->share('user', $user);

        return view('pages.user.password');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param $role
     * @param User $user
     * @return RedirectResponse
     * @throws \Illuminate\Validation\ValidationException
     * @noinspection PhpUnusedParameterInspection
     */
    public function update(Request $request, $role, User $user): RedirectResponse
    {
        $updateExistingUser = new UpdateExistingUser($user);

        $this->dispatchNow($updateExistingUser);

        return ($updateExistingUser->success())
            ? redirect()->route('back-office.user.index', $request->route('role'))->with('success', 'User Updated!')
            : redirect()->back()->withInput()->withErrors(['internal server error']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $role
     * @param User $user
     * @return RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy($role, User $user): RedirectResponse
    {
        $this->authorize('user.manage');

        $deleteExistingUser = new DeleteExistingUser($user);

        $this->dispatchNow($deleteExistingUser);

        return ($deleteExistingUser->success())
            ? redirect()->route('back-office.user.index', $role)->with('success', 'User Deleted!')
            : redirect()->back()->withInput()->withErrors(['internal server error']);
    }
}
