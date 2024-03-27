<?php

namespace App\Http\Controllers;

use App\Http\Requests\EditClinicRequest;
use App\Http\Requests\LoginRequest;
use App\Models\Clinic;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function login(LoginRequest $request)
    {
        $request = $request->validated();
        if (!$token = auth()->attempt($request)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $clinic = Clinic::find(auth()->user()->clinic_id);
        return response()->json(['token' => $token, 'user' => auth()->user(), 'clinic' => $clinic]);
    }

    public function editClinic(EditClinicRequest $request)
    {
        $clinic = $request->session()->get('clinic');
        $request = $request->validated();
        $clinic = Clinic::find($clinic->id);
        $clinic->update($request);
        return response()->json(['clinic' => $clinic]);
    }
}
