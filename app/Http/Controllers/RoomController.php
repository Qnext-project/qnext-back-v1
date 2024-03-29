<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateRoomRequest;
use App\Http\Requests\EditRoomRequest;
use App\Models\Floor;
use App\Models\Media;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RoomController extends Controller
{
    //
    public function create(CreateRoomRequest $request)
    {
        $clinic = $request->session()->get('clinic');
        $request = $request->validated();
        $request = [
            ...$request,
            'clinic_id' => $clinic->getId()
        ];
        $room = Room::create($request);
        return response()->json($room);
    }
    public function edit(Room $room,EditRoomRequest $request)
    {
        $request = $request->validated();
        $room = Room::find($room->id);
        $room->update($request);
        return response()->json($room);
    }

    public function index(Request $request)
    {
        $rooms = Room::all();
        return response()->json($rooms);
    }

    public function show(Room $room)
    {
        return response()->json($room);
    }

    public function remove(int $room)
    {
        $room = Room::find($room);
        $room->delete();
        return response()->noContent();
    }

    public function createFloor(Request $request)
    {
        $request = $request->validate([
            'name' => 'required|string'
        ]);
        $floor = Floor::create($request);
        return response()->json($floor);
    }
    public function editFloor(Request $request, Floor $floor)
    {
        $request = $request->validate([
            'name' => 'required|string'
        ]);
        $floor = $floor->update($request);
        return response()->json($floor);
    }

    public function floorList()
    {
        $floors = Floor::all();
        return response()->json($floors);
    }
}
