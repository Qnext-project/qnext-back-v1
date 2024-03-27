<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateExpertiseRequest;
use App\Http\Requests\EditExpertiseRequest;
use App\Models\Expertise;
use Illuminate\Http\Request;

class ExpertiseController extends Controller
{
    //
    public function create(CreateExpertiseRequest $request)
    {
        $clinic = $request->session()->get('clinic');
        $request = $request->validated();
        $request = [
            ...$request,
            'clinic_id' => $clinic->getId()
        ];
        $expertise = Expertise::create($request);
        return response()->json($expertise);
    }
    public function edit(Expertise $expertise, EditExpertiseRequest $request)
    {
        $request = $request->validated();
        $expertise = Expertise::find($expertise->id);
        $expertise->update($request);
        return response()->json($expertise);
    }

    public function index(Request $request)
    {
        $expertises = Expertise::all();
        return response()->json($expertises);
    }

    public function show(Expertise $expertise)
    {
        return response()->json($expertise);
    }

    public function remove(int $expertise)
    {
        $expertise = Expertise::find($expertise);
        $expertise->delete();
        return response()->noContent();
    }

}
