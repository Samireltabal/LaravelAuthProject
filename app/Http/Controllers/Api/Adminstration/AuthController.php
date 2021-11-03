<?php

namespace App\Http\Controllers\Api\Adminstration;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class AuthController extends Controller
{
    public function __construct() {
        $this->middleware(['role:admin,api']);
    }

    public function list_accounts(Request $request) {
        $accounts = User::query();
        if($request->has('verified') && $request->input('verified') != 'false') {
            $accounts = $accounts->where('email_verified_at', '!=', null);
        }
        if($request->has('search') && $request->input('search') != "null") {
            $term = $request->input('search');
            $accounts = $accounts->where('name', 'like', "%$term%")->orWhere('phone', 'like', "%$term%")->orWhere('email', 'like', "%$term%");
        }
        if($request->has('per_page') && $request->input('per_page')) {
            $per_page = $request->input('per_page');
        } else {
            $per_page = 10;
        }

        $accounts = $accounts->paginate($per_page);
        return response()->json($accounts, 200);
    }

    public function verify(Request $request) {
        $validation = $request->validate([
            'user_id' => 'required|exists:users,id'
        ]);

        $user = User::find($request->input('user_id'));
        $user->markEmailAsVerified();
        return response()->json(['message' => 'successfully updated'], 200);
    }
}
