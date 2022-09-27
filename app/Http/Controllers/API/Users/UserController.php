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
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    public function index()
    {
        // Get Params from search 
        $userName = \Request::get('name') ?: null;
        $userEmail = \Request::get('email') ?: null;
        $userGroupRole = \Request::get('user_group_role');
        if(!is_numeric($userGroupRole) && $userGroupRole === '') $userGroupRole = null;
        $userStatus = \Request::get('user_status');
        if(!is_numeric($userStatus) && $userStatus === '') $userStatus = null;

        $userName = preg_replace('/[^a-z0-9 _]+/i', '', $userName);
        $userEmail = preg_replace('/[^a-z0-9 _]+/i', '', $userEmail);

        $validator = Validator::make([
            'user_name' => $userName, 
            'user_email' => $userEmail,
            'user_status' => $userStatus,
            'user_group_role' => $userGroupRole,
        ], [
            'user_name' => 'nullable|string',
            'user_email' => 'nullable|string',
            'user_status' => 'nullable|integer',
            'user_group_role' => 'nullable|integer|exists:group_role,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid Inputs',
                'error' => $validator->errors()
            ], Response::HTTP_BAD_REQUEST);
        }

        $per_page = intval(\Request::get('per_page')) ?: 10;
        $users = User::where('is_delete', 0)->orderBy('created_at', 'desc');
        if(!empty($userName)) $users = $users->where('name', 'like', "%{$userName}%");
        if(!empty($userEmail)) $users = $users->where('email', 'like', "%{$userEmail}%");
        if(!is_null($userStatus)) $users = $users->where('is_active', $userStatus);
        if(!is_null($userGroupRole)) $users = $users->where('group_role', $userGroupRole);
        
        $users = $users->paginate($per_page);
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
            'name' => 'required|string|max:255|min:5',
            'group_role' => 'required|string|max:50',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => [
                'required',
                'string',
                'confirmed', 
                Password::min(8)->letters()->mixedCase()->numbers()->symbols()
            ],
            'is_active' => 'required|integer|between:0,1',
        ], [
            'email.unique' => 'Email đã được đăng ký.'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid Inputs',
                'error' => $validator->errors()
            ], Response::HTTP_UNAUTHORIZED);
        }
        // dd($validator);

        $item = new User();
        $item->name = $request->name;
        $item->email = $request->email;
        $item->group_role = $request->group_role;
        $item->is_active = $request->is_active;
        $item->is_delete = 0;
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
            'name' => 'required|string|max:255|min:5',
            'group_role' => 'required|string|max:50',
            'email' => 'required|string|email|max:255|unique:users,email,'.$user->id,
            'is_active' => 'required|integer|between:0,1',
        ], [
            'email.unique' => 'Email đã được đăng ký.'
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
