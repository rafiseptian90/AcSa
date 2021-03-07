<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Libs\Response;
use App\Models\Profile;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function __construct(){
        $this->middleware('auth.jwt');
    }

    /*
     * Update profile method
     * URI: '/profile/update'
     * Method: PUT
     * */
    public function update(Request $request){
        // define variable which will be used
        $user = auth()->user();
        $profile = Profile::whereUserId($user->id)->first();

        // request validation
        $this->reqValidation($request, $user);

        // catch request for update user
        $userReq = $request->only(['username', 'email', 'pin']);
        $profileReq = $request->except(['username', 'email', 'pin']);

        // update user
        $user->update($userReq);

        // if request has image
        if($request->hasFile('avatar')){
            if($profile->avatar !== NULL){
                cloudinary()->destroy($this->imageID($profile->avatar));
            }
            // upload file to cloudinary
            $profileReq['avatar'] = cloudinary()->upload($request->file('avatar')->getRealPath(), ['folder' => 'users'])->getSecurePath();
        }

        // update profile
        $profile->update($profileReq);

        // throw response
        return Response::success(['message' => 'Profile has been updated']);
    }

    private function reqValidation($request, $user){
        $this->validate($request, [
            'name' => 'required|regex:/^[a-z A-Z]+$/',
            'email' => 'required|unique:users,email,' . $user->id,
            'username' => 'nullable|unique:users,username,' . $user->id,
            'pin' => 'nullable|unique:users,pin,' . $user->id,
            'avatar' => 'image|mimes:jpg,png,jpeg|max:5120',
        ]);
    }

    private function imageID($image): array
    {
        $images = [];
        // get image ID
        $path = explode('/', $image);
        $imagePath = $path[count($path) - 2] . '/' . explode('.', $path[count($path) - 1])[0];

        array_push($images, $imagePath);
        return $images;
    }
}
