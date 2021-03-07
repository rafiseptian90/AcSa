<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Libs\Response;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Auth;
use Hash;

class AuthController extends Controller
{
    public function __construct(){
        $this->middleware('auth.jwt', ['except' => ['login']]);
    }

    /*
     * Login method
     * URI: '/auth/login'
     * Method: POST
     * */
    public function login(){
        $credentials = request(['email', 'password']);

        // get user login token
        if (!$token = auth()->attempt($credentials)) {
            // set response error
            return Response::error(['message' => 'Your credentials is wrong !']);
        }

        // update many login and last login
        auth()->user()->update([
            'many_login' => (int) auth()->user()->many_login + 1,
            'last_login' => Carbon::now()->toDateTimeString()
        ]);

        // set response success
        return $this->respondWithToken($token);
    }

    /*
     * Show profile
     * URI: '/auth/profile'
     * Method: POST
     * */
    public function profile(){
        $profile = User::with(['profile'])->findOrFail(auth()->user()->id);

        return Response::success([
            'message' => 'Data has been loaded',
            'data' => $profile
        ]);
    }

    /*
     * Logout method
     * URI: '/auth/logout'
     * Method: POST
     * */
    public function logout()
    {
        auth()->logout();

        return Response::success(['message' => 'Logout Successful']);
    }

    /*
     * Change password method
     * URI: '/auth/change-password'
     * Method: PUT
     * */
    public function changePassword(Request $request)
    {
        /*
         * Request validation
         * */
        $this->validate($request, [
            'current_password' => 'required',
            'new_password' => 'required',
        ]);
        /*
         * End request validation
         * */

        // matching request password with current password
        if(!Hash::check($request->current_password, auth()->user()->password)){
            // throw response
            return Response::error(['message' => 'Current password is not match !']);
        }

        // update user password
        auth()->user()->update([
            'password' => bcrypt($request->new_password)
        ]);

        // throw response
        return Response::success(['message' => 'Your password has been change']);
    }

    /*
     * Token response
     * */
    protected function respondWithToken($token)
    {
        return Response::success([
            'message' => 'Login Successful',
            'accessToken'=> $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            'userData' => User::with(['profile'])->whereId(auth()->user()->id)->first(),
        ]);
    }
}
