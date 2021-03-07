<?php

namespace App\Libs;

class Response{
    // success response
    public static function success($response){
        return response()->json($response, 200);
    }
    // error response
    public static function error($response){
        return response()->json($response, 422);
    }
    // forbidden response
    public static function forbidden($response){
        return response()->json($response, 403);
    }
    // notfound response
    public static function notfound($response){
        return response()->json($response, 404);
    }
    // invalid response
    public static function invalid($response){
        return response()->json($response, 401);
    }
}
