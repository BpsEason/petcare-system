<?php

namespace App\Http\Controllers;

use App\Models\HealthRecord;
use App\Models\Pet;
use Illuminate\Http\Request;

class HealthRecordController extends Controller
{
    public function index(Request $request)
    {
        return HealthRecord::whereHas('pet', function ($query) use ($request) {
            $query->where('user_id', $request->user()->id);
        })->get();
    }

    public function indexByPet(Pet $pet)
    {
        $this->authorize('view', $pet);
        return $pet->healthRecords;
    }

    public function store(Request $request)
    {
        $request->validate([
            'pet_id' => 'required|exists:pets,id',
            'record_date' => 'required|date',
            'type' => 'required|string|max:255', // e.g., 'Vaccination', 'Check-up', 'Medication'
            'description' => 'nullable|string',
            'veterinarian' => 'nullable|string|max:255',
            'next_due_date' => 'nullable|date',
        ]);

        $pet = Pet::find($request->pet_id);
        $this->authorize('update', $pet); // Check if the user owns the pet

        $healthRecord = HealthRecord::create($request->all());

        return response()->json(['message' => __('messages.health_record_added'), 'record' => $healthRecord], 201);
    }

    public function show(HealthRecord $healthRecord)
    {
        $this->authorize('view', $healthRecord);
        return $healthRecord;
    }

    public function update(Request $request, HealthRecord $healthRecord)
    {
        $this->authorize('update', $healthRecord);

        $request->validate([
            'record_date' => 'sometimes|required|date',
            'type' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'veterinarian' => 'nullable|string|max:255',
            'next_due_date' => 'nullable|date',
        ]);

        $healthRecord->update($request->all());

        return response()->json(['message' => __('messages.health_record_updated'), 'record' => $healthRecord]);
    }

    public function destroy(HealthRecord $healthRecord)
    {
        $this->authorize('delete', $healthRecord);
        $healthRecord->delete();
        return response()->json(['message' => __('messages.health_record_deleted')], 204);
    }
}
