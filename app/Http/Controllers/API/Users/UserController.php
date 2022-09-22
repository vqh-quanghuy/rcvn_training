<?php

namespace App\Http\Controllers\API\Users;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{

    public function index()
    {
        $users = User::where('is_delete', 0)->paginate(5);
        $roles = DB::table('group_role')->select('id', 'role_name')->get();

        return response()->json([
            'status' => true,
            'roles' => $roles,
            'data' => $users,
        ], Response::HTTP_OK);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'group_role' => 'required|string|max:50',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|confirmed|min:8',
            'is_active' => 'required|integer|between:0,1',
            'is_delete' => 'required|integer|between:0,1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid Inputs',
                'error' => $validator->errors()
            ], Response::HTTP_UNAUTHORIZED);
        }

        $item = new User();
        $item->name = $request->name;
        $item->email = $request->email;
        $item->group_role = $request->group_role;
        $item->is_active = $request->is_active;
        $item->is_delete = $request->is_delete;
        $item->password = Hash::make($request->password);
        $item->save();

        return response()->json([
            'status' => true,
            'message' => 'Created',
            'user' => $item,
        ], Response::HTTP_CREATED);
    }

    public function detail(User $user)
    {
        $roles = DB::table('group_role')->select('id', 'role_name')->get();
        return response()->json([
            'status' => true,
            'roles' => $roles,
            'data' => $user,
        ], Response::HTTP_OK);
    }

    public function update(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'group_role' => 'required|string|max:50',
            'email' => 'required|string|email|max:255|unique:users,email,'.$user->id,
            'is_active' => 'required|integer|between:0,1',
            'is_delete' => 'required|integer|between:0,1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid Inputs',
                'error' => $validator->errors()
            ], Response::HTTP_BAD_REQUEST);
        }
        $inputs = $request->post();
        // $inputs['password'] = Hash::make($request->post('password'));
        $user->fill($inputs)->save();   

        return response()->json([
            'status' => true,
            'message' => 'Updated',
        ], Response::HTTP_ACCEPTED);
    }

    public function destroy(User $user)
    {
        // $user->delete();
        $user->fill(['is_delete'=>1])->save();  
        return response()->json([
            'status' => true,
            'message' => 'Deleted',
        ], Response::HTTP_ACCEPTED);
    }

    public function deactivate(Request $request, User $user)
    {
        $entry = DB::table('users')->find($request->id);

        if($entry !== null) {
            $isActive = $entry->is_active === 0 ? 1 : 0;

            DB::table('users')
            ->where('id', $request->id)
            ->update(array('is_active' => $isActive));

            return response()->json([
                'status' => true,
                'message' => $isActive === 1 ? 'Unlocked' : 'Locked',
            ], Response::HTTP_ACCEPTED);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Invalid Inputs',
            ], Response::HTTP_BAD_REQUEST);
        }
    }
}
