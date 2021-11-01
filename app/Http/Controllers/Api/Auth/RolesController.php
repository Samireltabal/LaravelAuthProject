<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;


class RolesController extends Controller
{
    //
    public function __construct() {
        $this->middleware(['role:admin']);
    }

    public function create_role(Request $request) {
        $validation = $request->validate([
            'role_name' => 'required|unique:roles,name'
        ]);

        $role = Role::create(['guard_name' => 'api', 'name' => $request->input('role_name')]);
        return response()->json($role, 201);
    }

    public function list_roles() {
        $roles = Role::all();
        return response()->json($roles, 200);
    }

    public function attach_role(Request $request) {
        $validation = $request->validate([
            'user_id' => 'required|exists:users,id',
            'role_name' => 'required|exists:roles,name',
        ]);
        $user = User::find($request->input('user_id'));
        $role = Role::findByName($request->input('role_name'));
        $user->syncRoles($role);
        return $user;
    }

    public function create_permission() {
        
    }

    public function list_permissions() {
        $permissions = Permission::all();
        return response()->json($permissions, 200);
    }


}
