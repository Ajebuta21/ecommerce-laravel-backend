<?php

namespace App\Http\Controllers;

use App\Models\Region;
use Illuminate\Http\Request;

class RegionController extends Controller
{
    public function index()
    {
        $region = Region::all();
        return response()->json($region);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:regions',
            'fee' => 'required|numeric'
        ]);

        Region::create($validated);

        return response()->json(['message' => 'Region created'], 201);
    }

    public function show($id)
    {
        $region = Region::findOrFail($id);

        return response()->json($region);
    }

    public function update(Request $request, $id)
    {
        $region = Region::findOrFail($id);

        $validated = $request->validate([
            'name' =>
            'required|string|unique:regions,name,' . $id,
            'fee' => 'required|numeric'
        ]);

        $region->update($validated);

        return response()->json(['message' => 'Region updated'], 201);
    }

    public function destroy($id)
    {
        $region = Region::findOrFail($id);

        $region->delete();

        return response()->json(['message' => 'Region deleted'], 201);
    }
}
