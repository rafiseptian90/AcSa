<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Libs\Response;
use App\Models\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ApplicationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        // get all application
        $apps = Application::withCount('accounts')
                            ->filter(request()->all())
                            ->get();

        // throw response
        return Response::success([
            'message' => 'Data has been loaded',
            'data' => $apps
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function store(Request $request): JsonResponse
    {
        // request validation
        $this->reqValidation($request);

        // catch all request
        $requests = $request->all();
        $requests['app_type_id'] = $this->getType(Str::slug(request('type')));

        // if request has image
        if($request->hasFile('logo')){
            $requests['logo'] = cloudinary()->upload($request->file('logo')->getRealPath(), ['folder' => 'applications'])->getSecurePath();
        }

        // store new application
        $app = Application::create($requests);

        // throw response
        return Response::success([
            'message' => 'Application has been created',
            'data' => $app
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param $id
     * @return JsonResponse
     */
    public function show($id): JsonResponse
    {
        $app = Application::with(['accounts'])->withCount('accounts')->findOrFail($id);

        // throw response
        return Response::success([
            'message' => 'Data has been loaded',
            'data' => $app
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
        // find app
        $app = Application::findOrFail($id);

        // request validation
        $this->reqValidation($request);

        // catch all request
        $requests = $request->all();

        // if request has image
        if($request->hasFile('logo')){
            // if current logo is not null
            if($app->logo !== NULL){
                cloudinary()->destroy($this->imageID($app->logo));
            }

            // store image
            $requests['logo'] = cloudinary()->upload($request->file('logo')->getRealPath(), ['folder' => 'applications'])->getSecurePath();
        }

        // update app
        $app->update($requests);

        // throw response
        return Response::success([
            'message' => 'Application has been updated',
            'data' => $app
        ]);
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
        $app = Application::findOrFail($id);

        // if current logo is not null
        if($app->logo !== NULL){
            cloudinary()->destroy($this->imageID($app->logo));
        }

        // destroy application
        $app->delete();

        // throw response
        return Response::success(['message' => 'Application has been deleted']);
    }

    private function reqValidation($request){
        $this->validate($request, [
            'name' => 'required',
            'logo' => 'image|mimes:jpg,png,jpeg|max:5120'
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

    private function getType($slug){
        return \App\Models\ApplicationType::whereSlug($slug)->first()['id'];
    }
}
