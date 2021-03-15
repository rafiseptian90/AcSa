<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Libs\Response;
use App\Models\ApplicationType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApplicationTypeController extends Controller
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
        // get all application types
        $app_types = ApplicationType::all();

        // throw response
        return Response::success([
            'message' => 'Data has been loaded',
            'data' => $app_types
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
        $this->reqValidation($request);

        // catch all request
        $requests = $request->all();

        // if request has image
        if($request->hasFile('logo')){
            $requests['logo'] = cloudinary()->upload($request->file('logo')->getRealPath(), ['folder' => 'application_types'])->getSecurePath();
        }

        // store new application type
        $types = ApplicationType::create($requests);

        // throw response
        return Response::success([
            'message' => 'Application type has been created',
            'data' => $types
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
        // find the data
        $app_type = ApplicationType::withCount('applications')->findOrFail($id);

        // throw response
        return Response::success([
            'message' => 'Data has been loaded',
            'data' => $app_type
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
        // find app type
        $app_type = ApplicationType::findOrFail($id);

        // request validation
        $this->reqValidation($request);

        // catch all request
        $requests = $request->all();

        // if request has image
        if($request->hasFile('logo')){
            // if current logo is not null
            if($app_type->logo !== NULL){
                cloudinary()->destroy($this->imageID($app_type->logo));
            }

            // store image
            $requests['logo'] = Storage::disk('public')->put('/application_types', $request->file('logo'));
        }

        // update app type
        $app_type->update($requests);

        // throw response
        return Response::success([
            'message' => 'Application type has been updated',
            'data' => $app_type
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
        // find app type
        $app_type = ApplicationType::findOrFail($id);

        // if current logo is not null
        if($app_type->logo !== NULL){
            cloudinary()->destroy($this->imageID($app_type->logo));
        }

        // destroy application type
        $app_type->delete();

        // throw response
        return Response::success(['message' => 'Application type has been deleted']);
    }

    // validation method
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
}
