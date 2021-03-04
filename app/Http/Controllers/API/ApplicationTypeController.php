<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Libs\Response;
use App\Models\ApplicationType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Storage;

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
        $this->validate($request, [
            'name' => 'required',
            'logo' => 'image|mimes:jpg,png,jpeg|max:5120'
        ]);

        // catch all request
        $requests = $request->all();

        // if request has image
        if($request->hasFile('logo')){
            $requests['logo'] = Storage::disk('public')->put('/application_types', $request->file('logo'));
        }

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
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $type = ApplicationType::withCount('applications')->findOrFail($id);

        // throw response
        return Response::success([
            'message' => 'Data has been loaded',
            'data' => $type
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(Request $request, int $id): JsonResponse
    {
        // find app type
        $app_type = ApplicationType::findOrFail($id);

        // request validation
        $this->validate($request, [
            'name' => 'required',
            'logo' => 'image|mimes:jpg,png,jpeg|max:5120'
        ]);

        // catch all request
        $requests = $request->all();

        // if request has image
        if($request->hasFile('logo')){
            // if current logo is not null
            if($app_type->logo !== NULL){
                Storage::disk('public')->delete($app_type->logo);
            }

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
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        // find app type
        $app_type = ApplicationType::findOrFail($id);

        // if current logo is not null
        if($app_type->logo !== NULL){
            Storage::disk('public')->delete($app_type->logo);
        }

        $app_type->delete();

        // throw response
        return Response::success(['message' => 'Application type has been deleted']);
    }
}
