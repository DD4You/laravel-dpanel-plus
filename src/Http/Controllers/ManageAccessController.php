<?php

namespace DD4You\Dpanel\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Str;

class ManageAccessController extends Controller
{
    public function index()
    {
        abort_if(!auth()->user()->can('manage-access'), 403, 'You don\'t have permission to access this.');

        $bgColorsForWhiteText = [
            '#497853',
            '#9813AA',
            '#8928D6',
            '#6B3EAC',
            '#C92581',
            '#1069D5',
            '#066B5E',
            '#2D424D',
            '#0D2FB9',
            '#334AB8',
            '#750774',
            '#E8060D',
            '#0821FA',
            '#8E3A03',
            '#52409C',
            '#893189',
            '#4B3DB5',
            '#1D34D5',
            '#9A372C',
            '#0D1C23',
            '#D225AA',
            '#1A7580',
            '#146020',
            '#7E134D',
            '#3D4F1D',
            '#7D15B9',
            '#0C3C80',
            '#703AC3',
            '#9D095A',
            '#1B6DAE',
            '#696A89',
            '#934816',
            '#0663E4'
        ];

        $roles = Role::with('permissions')->paginate(5);

        $users = User::with('roles')->paginate(10);

        // return $users;
        $permissions = Permission::orderBy('name', 'asc')->get();

        return view('dpanel::manage_access', compact('roles', 'users', 'permissions', 'bgColorsForWhiteText'));
    }

    public function store(Request $request)
    {
        abort_if(!auth()->user()->can('manage-access'), 403, 'You don\'t have permission to access this.');

        if ($request->type == 'role') {

            $roleName = Str::slug($request->role_name);

            $role = Role::firstOrCreate(['name' => $roleName]);

            if ($request->permissions) $role->syncPermissions($request->permissions);

            return back()->withSuccess('New Role Created Successfully.');
        } elseif ($request->type == 'user') {
            $request->validate([
                'first_name' => 'required',
                'last_name' => 'required',
                'password' => 'required',
                'email' => 'required|email|unique:users,email'
            ]);
            $user = User::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'email_verified_at' => now(),
                'password' => bcrypt($request->password)
            ]);

            if ($request->roles) $user->syncRoles($request->roles);

            return back()->withSuccess('New User Created Successfully.');
        }
    }

    public function update(Request $request, $id)
    {
        abort_if(!auth()->user()->can('manage-access'), 403, 'You don\'t have permission to access this.');

        if ($request->type == 'role') {

            $roleName = Str::slug($request->role_name);

            $role = Role::firstOrCreate(['name' => $roleName]);

            if ($request->permissions) $role->syncPermissions($request->permissions);

            return back()->withSuccess('Role Updated Successfully.');
        } elseif ($request->type == 'user') {

            $request->validate([
                'first_name' => 'required',
                'last_name' => 'required',
                'email' => 'required|email|unique:users,email,' . $id
            ]);

            $user = User::find($id);

            $user->first_name = $request->first_name;

            $user->last_name = $request->last_name;

            $user->email = $request->email;

            if ($request->password) $user->password = bcrypt($request->password);

            $user->save();

            if ($request->roles) $user->syncRoles($request->roles);

            return back()->withSuccess('User Updated Successfully.');
        }
    }
}
