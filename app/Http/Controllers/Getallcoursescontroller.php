<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Services\ApiResponseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class Getallcoursescontroller extends Controller
{
    protected $apiResponse;

    public function __construct(ApiResponseService $apiResponse)
    {
        $this->apiResponse = $apiResponse;
    }

    /**
     * Get all courses
     */
    public function index()
    {
        try {
            $courses = Course::latest()->get();
            return $this->apiResponse->success($courses, 'Courses retrieved successfully', 200);
        } catch (\Exception $e) {
            Log::error('Getallcoursescontroller index error: ' . $e->getMessage());
            return $this->apiResponse->error('Failed to retrieve courses', 500);
        }
    }
}
