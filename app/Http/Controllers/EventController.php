<?php

namespace App\Http\Controllers;

use App\Models\event;
use App\Services\ApiResponseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class EventController extends Controller
{
    protected $apiResponse;

    public function __construct(ApiResponseService $apiResponse)
    {
        $this->apiResponse = $apiResponse;
    }

    public function index()
    {
        $events = event::all();

        if ($events->isEmpty()) {
            return $this->apiResponse->error('No events found');
        }

        return $this->apiResponse->success([
            'message' => 'Events Retrieved Successfully',
            'data' => $events,
        ], 200);
    }

    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'Name' => 'required|string|min:1|max:255',
            'Date' => 'required|date',
            'Description' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse->error($validator->errors());
        }

        $data = $validator->validated();
        $data['CreatedBy'] = Auth::user()->id;
        $data['CreatedDate'] = now();

        $event = event::create($data);

        return $this->apiResponse->success([
            'data' => $event,
            'message' => 'Event Created Successfully',
        ], 201);
    }

    public function show($id)
    {
        $event = event::findOrFail($id);

        return $this->apiResponse->success([
            'message' => 'Event Retrieved Successfully',
            'data' => $event,
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $event = event::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'Name' => 'sometimes|string|min:1|max:255',
            'Date' => 'sometimes|date',
            'Description' => 'sometimes|string',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse->error($validator->errors());
        }

        $event->update($validator->validated());

        return $this->apiResponse->success([
            'message' => 'Event Updated Successfully',
            'data' => $event,
        ]);
    }

    public function destroy($id)
    {
        $event = event::findOrFail($id);
        $event->delete();

        return $this->apiResponse->success([
            'message' => 'Event Deleted Successfully',
        ]);
    }
}
