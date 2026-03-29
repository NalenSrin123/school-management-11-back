<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Services\ApiResponseService;
use Illuminate\Support\Facades\Log;

class CourseDetailController extends Controller
{
    protected $apiResponse;

    public function __construct(ApiResponseService $apiResponse)
    {
        $this->apiResponse = $apiResponse;
    }

    /**
     * GET /api/course-detail/{id}
     * Course Detail for user 
     */
    public function getCourseDetail($id)
    {
        try {
            $course = Course::with('creator')->find($id);

            if (!$course) {
                return $this->apiResponse->error('Course not found', 404);
            }

            $courseDetail = [
                'id'           => $course->id,
                'name'         => $course->name,
                'description'  => $course->description,
                'duration'     => $course->duration,
                'status'       => $course->status,
                'creator_name' => $course->creator ? $course->creator->name : null,
                'created_at'   => $course->created_at,
                'updated_at'   => $course->updated_at,
            ];

            return $this->apiResponse->success($courseDetail, 'Course detail retrieved successfully', 200);

        } catch (\Exception $e) {
            Log::error('Course detail error: ' . $e->getMessage());
            return $this->apiResponse->error('Failed to retrieve course detail', 500);
        }
    }
}