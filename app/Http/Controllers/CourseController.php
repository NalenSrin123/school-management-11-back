<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Services\ApiResponseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class CourseController extends Controller
{
    protected $apiResponse;

    public function __construct(ApiResponseService $apiResponse)
    {
        $this->apiResponse = $apiResponse;
    }

    /**
     * Display a listing of courses
     */
    public function index()
    {
        try {
            $courses = Course::latest()->get();
            return $this->apiResponse->success($courses, 'Courses retrieved successfully', 200);
        } catch (\Exception $e) {
            Log::error('Course index error: ' . $e->getMessage());
            return $this->apiResponse->error('Failed to retrieve courses', 500);
        }
    }

    /**
     * Create a new course
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'duration' => 'nullable|string|max:255',
            'status' => 'nullable|string|in:Active,Inactive',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse->error('Validation failed', 422, $validator->errors());
        }

        try {

            $course = Course::create([
                'name' => $request->name,
                'description' => $request->description,
                'duration' => $request->duration,
                'status' => $request->status ?? 'Active',
            ]);

            return $this->apiResponse->success($course, 'Course created successfully', 201);
        } catch (\Exception $e) {
            Log::error('Course store error: ' . $e->getMessage());
            return $this->apiResponse->error('Failed to create course: ' . $e->getMessage(), 400);
        }
    }

    /**
     * Display details of a single course
     */
    public function show($id)
    {
        try {
            $course = Course::find($id);

            if (!$course) {
                return $this->apiResponse->error('Course not found', 404);
            }

            return $this->apiResponse->success($course, 'Course retrieved successfully', 200);
        } catch (\Exception $e) {
            Log::error('Course show error: ' . $e->getMessage());
            return $this->apiResponse->error('Failed to retrieve course details', 500);
        }
    }

    /**
     * Update course status or details
     */
    public function update(Request $request, $id)
    {
        try {
            $course = Course::find($id);

            if (!$course) {
                return $this->apiResponse->error('Course not found', 404);
            }

            $validator = Validator::make($request->all(), [
                'name' => 'sometimes|required|string|max:255',
                'description' => 'nullable|string',
                'duration' => 'nullable|string|max:255',
                'status' => 'nullable|string|in:Active,Inactive',
            ]);

            if ($validator->fails()) {
                return $this->apiResponse->error('Validation failed', 422, $validator->errors());
            }

            $course->update($request->only(['name', 'description', 'duration', 'status']));

            return $this->apiResponse->success($course, 'Course updated successfully', 200);
        } catch (\Exception $e) {
            Log::error('Course update error: ' . $e->getMessage());
            return $this->apiResponse->error('Failed to update course', 500);
        }
    }

    /**
     * Delete a course
     */
    public function destroy($id)
    {
        try {
            $course = Course::find($id);

            if (!$course) {
                return $this->apiResponse->error('Course not found', 404);
            }

            $course->delete();

            return $this->apiResponse->success(null, 'Course deleted successfully', 200);
        } catch (\Exception $e) {
            Log::error('Course delete error: ' . $e->getMessage());
            return $this->apiResponse->error('Failed to delete course', 500);
        }
    }
}

