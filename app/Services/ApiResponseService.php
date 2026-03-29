<?php
namespace App\Services;

class ApiResponseService
{
    // Send a success response
    public function success($data = null, $message = "Success", $status = 200)
    {
        return response()->json([
            "status" => true,
            "message" => $message,
            "data" => $data
        ], $status);
    }

    // Send an error response
    public function error($message = "Error", $status = 400, $errors = null)
    {
        return response()->json([
            "status" => false,
            "message" => $message,

        ], $status);
    }
}
