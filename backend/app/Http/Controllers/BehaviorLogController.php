<?php

namespace App\Http\Controllers;

use App\Models\BehaviorLog;
use App\Models\Pet;
use Illuminate\Http\Request;

class BehaviorLogController extends Controller
{
    public function indexByPet(Pet $pet)
    {
        $this->authorize('view', $pet);
        return $pet->behaviorLogs;
    }

    public function store(Request $request)
    {
        $request->validate([
            'pet_id' => 'required|exists:pets,id',
            'log_date' => 'required|date',
            'behavior' => 'nullable|string|max:255',
            'emotion' => 'nullable|string|max:255',
            'appetite' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $pet = Pet::find($request->pet_id);
        $this->authorize('update', $pet); // Check if the user owns the pet

        $behaviorLog = BehaviorLog::create($request->all());

        return response()->json(['message' => __('messages.behavior_log_added'), 'log' => $behaviorLog], 201);
    }

    public function update(Request $request, BehaviorLog $behaviorLog)
    {
        $this->authorize('update', $behaviorLog->pet);

        $request->validate([
            'log_date' => 'sometimes|required|date',
            'behavior' => 'nullable|string|max:255',
            'emotion' => 'nullable|string|max:255',
            'appetite' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $behaviorLog->update($request->all());

        return response()->json(['message' => __('messages.behavior_log_updated'), 'log' => $behaviorLog]);
    }

    public function destroy(BehaviorLog $behaviorLog)
    {
        $this->authorize('delete', $behaviorLog->pet);
        $behaviorLog->delete();
        return response()->json(['message' => __('messages.behavior_log_deleted')], 204);
    }
}
