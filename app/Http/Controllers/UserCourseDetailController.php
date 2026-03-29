<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Services\ApiResponseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UserCourseDetailController extends Controller
{
    protected $apiResponse;

    public function __construct(ApiResponseService $apiResponse)
    {
        $this->apiResponse = $apiResponse;
    }
    //Course Detail for User can Acess
    public function courseDetail($id){
        try {
            $course = Course::select('id', 'name', 'description', 'duration')->where('status', 'Active')->find($id);
            if (!$course) {
                return $this->apiResponse->error('Course not found', 404);
            }

            return $this->apiResponse->success($course, 'Course retrieved successfully', 200);
        } catch (\Exception $e) {
            Log::error('Course show error: ' . $e->getMessage());
            return $this->apiResponse->error('Failed to retrieve course details', 500);
        }
    }
}
