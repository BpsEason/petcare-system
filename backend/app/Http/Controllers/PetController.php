<?php

namespace App\Http\Controllers;

use App\Models\Pet;
use Illuminate\Http\Request;

class PetController extends Controller
{
    public function index(Request $request)
    {
        return $request->user()->pets;
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'species' => 'required|string|max:255',
            'breed' => 'nullable|string|max:255',
            'date_of_birth' => 'nullable|date',
        ]);

        $pet = $request->user()->pets()->create($request->all());

        return response()->json(['message' => __('messages.pet_added'), 'pet' => $pet], 201);
    }

    public function show(Pet $pet)
    {
        $this->authorize('view', $pet);
        return $pet;
    }

    public function update(Request $request, Pet $pet)
    {
        $this->authorize('update', $pet);

        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'species' => 'sometimes|required|string|max:255',
            'breed' => 'nullable|string|max:255',
            'date_of_birth' => 'nullable|date',
        ]);

        $pet->update($request->all());

        return response()->json(['message' => __('messages.pet_updated'), 'pet' => $pet]);
    }

    public function destroy(Pet $pet)
    {
        $this->authorize('delete', $pet);
        $pet->delete();
        return response()->json(['message' => __('messages.pet_deleted')], 204);
    }

    public function setDefaultPet(Request $request, Pet $pet)
    {
        $this->authorize('update', $pet);

        $request->user()->pets()->update(['is_default' => false]);

        $pet->is_default = true;
        $pet->save();

        return response()->json(['message' => __('messages.default_pet_set'), 'pet' => $pet]);
    }
}
