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
                    })->get();

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
    public function store(Request $request)
    {
        // request validation
        $this->reqValidation($request);

        // catch all request
        $requests = $request->all();

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
     * @param  $id
     * @return JsonResponse
     */
    public function show($id): JsonResponse
    {
        // find data
        $account = Account::with(['app', 'user'])->findOrFail($id);

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
     * @param $id
     * @return JsonResponse
     */
    public function update(Request $request, $id): JsonResponse
    {
        // find data
        $account = Account::findOrFail($id);

        // check if the account user id isn't match with user that logged in
        if($account->user_id !== auth()->user()->id){
            return Response::forbidden([
                'message' => 'Access forbidden !'
            ]);
        }

        // if request has image
        if($request->hasFile('photo')){
            // if current photo is not null
            if($account->photo !== NULL){
                cloudinary()->destroy($this->imageID($account->photo));
            }

            // store image
            $requests['photo'] = cloudinary()->upload($request->file('photo')->getRealPath(), ['folder' => 'accounts'])->getSecurePath();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $id
     * @return JsonResponse
     */
    public function destroy($id): JsonResponse
    {
        // find app
        $account = Account::findOrFail($id);

        // if current logo is not null
        if($account->logo !== NULL){
            cloudinary()->destroy($this->imageID($account->photo));
        }

        // destroy account
        $account->delete();

        // throw response
        return Response::success(['message' => 'Account has been deleted']);
    }

    private function reqValidation($request){
        $this->validate($request, [
            'description' => 'string|required|max:191',
            'password' => 'required',
            'photo' => 'image|mimes:jpg,png,jpeg|max:5120'
        ]);
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
}
