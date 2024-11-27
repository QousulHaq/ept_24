<?php

namespace App\View\Components\Sidebar;

use App\Entities\Account\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\View\Component;
use Silber\Bouncer\Database\Role;

class ManageUser extends Component
{
    public User $user;
    public Collection $roles;
    public string $activeRole;

    /**
     * Create a new component instance.
     *
     * @param \Illuminate\Http\Request $request
     */
    public function __construct(Request $request)
    {
        $this->user = $request->user();
        $this->activeRole = (string) $request->route('role', '');

        $this->roles = Role::query()->where('id', '>=', $this->user->roles->min->id)->get();
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        return view('partials.app.sidebar.user');
    }
}
