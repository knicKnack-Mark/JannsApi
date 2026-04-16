<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Room;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    public function index()
    {
        return Room::latest()->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required',
            'price' => 'required|numeric',
            'max_pax' => 'required|integer',
            'image' => 'nullable|image',
            'available' => 'boolean'
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('rooms', 'public');
        }

        return Room::create($data);
    }

    public function update(Request $request, $id)
    {
        $room = Room::findOrFail($id);

        $data = $request->validate([
            'name' => 'required',
            'price' => 'required|numeric',
            'max_pax' => 'required|integer',
            'image' => 'nullable|image',
            'available' => 'boolean'
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('rooms', 'public');
        }

        $room->update($data);

        return $room;
    }

    public function destroy($id)
    {
        Room::destroy($id);
        return response()->json(['message' => 'Deleted']);
    }
}
