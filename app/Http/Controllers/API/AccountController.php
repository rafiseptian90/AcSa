<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Account;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Libs\Response;

class AccountController extends Controller
{
    public function __construct(){
        $this->middleware('auth.jwt');
    }
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $accounts = Account::whereHas('user', function($user){
                                $user->whereId(auth()->user()->id);
                            })
                            ->whereAppId(request('app_id'))
                            ->get();

        // throw response
        return Response::success([
            'message' => 'Data has been loaded',
            'data' => $accounts
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        // request validation
        $this->reqValidation($request, 'POST', $request->app_id);

        // catch all request
        $requests = $request->all();
        $requests['app_id'] = $this->getAppID($request->app_id);

        // if request has image
        if($request->hasFile('photo')){
            $requests['photo'] = cloudinary()->upload($request->file('photo')->getRealPath(), ['folder' => 'accounts'])->getSecurePath();
        }

        // store new account
        $account = Account::create($requests);

        // throw response
        return Response::success([
            'message' => 'Account has been created',
            'data' => $account
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  $slug
     * @return JsonResponse
     */
    public function show($slug): JsonResponse
    {
        // find data
        $account = Account::with(['app', 'user'])
                            ->whereAppId($this->getAppID(request()->app_id))
                            ->whereUsername($slug)
                            ->orWhere('email', $slug)
                            ->first();

        // check existing data
        if(!$account){
            return Response::notfound([
                'message' => 'Data not found !'
            ]);
        }

        // check if the account user id isn't match with user that logged in
        if($account->user_id !== auth()->user()->id){
            return Response::forbidden([
                'message' => 'Access forbidden !'
            ]);
        }

        // throw response
        return Response::success([
            'message' => 'Data has been loaded',
            'data' => $account
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param $slug
     * @return JsonResponse
     */
    public function update(Request $request, $slug): JsonResponse
    {
        // find data
        $account = Account::with(['app', 'user'])
                            ->whereAppId($this->getAppID(request()->app_id))
                            ->whereUsername($slug)
                            ->orWhere('email', $slug)
                            ->first();

        // check existing data
        if(!$account){
            return Response::notfound([
                'message' => 'Data not found !'
            ]);
        }

        // check if the account user id isn't match with user that logged in
        if($account->user_id !== auth()->user()->id){
            return Response::forbidden([
                'message' => 'Access forbidden !'
            ]);
        }

        $this->reqValidation($request, 'PUT', $request->app_id, $account->id);

        // get requests
        $requests = $request->all();
        $requests['app_id'] = $this->getAppID($request->app_id);

        // if request has image
        if($request->hasFile('photo')){
            // if current photo is not null
            if($account->photo !== NULL){
                cloudinary()->destroy($this->imageID($account->photo));
            }

            // store image
            $requests['photo'] = cloudinary()->upload($request->file('photo')->getRealPath(), ['folder' => 'accounts'])->getSecurePath();
        }

        // update account
        $account->update($requests);

        // throw response
        return Response::success([
            'message' => 'Account has been updated',
            'data' => $account
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $slug
     * @return JsonResponse
     */
    public function destroy($slug): JsonResponse
    {
        // find data
        $account = Account::with(['app', 'user'])
                            ->whereAppId($this->getAppID(request()->app_id))
                            ->whereUsername($slug)
                            ->orWhere('email', $slug)
                            ->first();

        // check existing data
        if(!$account){
            return Response::notfound([
                'message' => 'Data not found !'
            ]);
        }

        // if current logo is not null
        if($account->logo !== NULL){
            cloudinary()->destroy($this->imageID($account->photo));
        }

        // destroy account
        $account->delete();

        // throw response
        return Response::success(['message' => 'Account has been deleted']);
    }

    private function reqValidation($request, $method, $app_slug, $id = null){
        if($method === 'POST'){
            $this->validate($request, [
                'app_id' => 'required',
                'description' => 'string|required|max:191',
                'password' => 'required',
                'photo' => 'image|mimes:jpg,png,jpeg|max:5120',
                'username' => "required_if:email,null|unique:accounts,username,NULL,id,app_id,". $this->getAppID($app_slug),
                'email' => "required_if:username,null|unique:accounts,email,NULL,id,app_id,". $this->getAppID($app_slug)
            ], [
                'username.required_if' => 'Username is required when the email not set',
                'email.required_if' => 'Email is required when the username not set',
            ]);
        }
        else if($method === 'PUT'){
            $this->validate($request, [
                'app_id' => 'required',
                'description' => 'string|required|max:191',
                'photo' => 'image|mimes:jpg,png,jpeg|max:5120',
                'username' => "required_if:email,null|unique:accounts,username,". $id .",id,app_id,". $this->getAppID($app_slug),
                'email' => "required_if:username,null|unique:accounts,email,". $id .",id,app_id,". $this->getAppID($app_slug)
            ]);
        }
    }

    // get image ID
    private function imageID($image): array
    {
        $images = [];
        // get image ID
        $path = explode('/', $image);
        $imagePath = $path[count($path) - 2] . '/' . explode('.', $path[count($path) - 1])[0];

        array_push($images, $imagePath);
        return $images;
    }

    // get app id by slug
    private function getAppID($slug){
        $app = \App\Models\Application::whereSlug($slug)->first();

        if(!$app){
            return '';
        }

        return $app->id;
    }
}
