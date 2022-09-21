<?php
namespace App\Http\Controllers\Api\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Response;

use App\Models\User;

class UserAuthController extends Controller
{

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:8'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid Inputs',
                'error' => $validator->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        if (Auth::attempt(['email'=>$request->email, 'password'=>$request->password])) {
            /** @var \App\Models\User $user **/
            $user = Auth::user();
            $token = $user->createToken('rcvn2012')->plainTextToken;
            $minutes = 1440;
            $timestamp = now()->addMinute($minutes);
            $expires_at = date('M d, Y H:i A', strtotime($timestamp));
            return response()->json([
                'status' => true,
                'message' => 'Login successful',
                'user' => $user,
                'access_token' => $token,
                'token_type' => 'bearer',
                'expires_at' => $expires_at
            ], Response::HTTP_OK);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Invalid Credentials',
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    public function logout(Request $request) {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status' => true,
            'message' => 'Logged out successful',
        ], Response::HTTP_OK);
    }
}