<?php

namespace App\Http\Controllers;

use App\Models\Media;
use Illuminate\Http\Request;
use App\Http\Requests\UploadAudioRequest;


class MediaController extends Controller
{
    //
    public function index(string $type)
    {
        $media = Media::where('url', 'like', '%' . $type . '%')->get();
        return response()->json($media);
    }
    public function store(Request $request, string $type)
    {
        $clinic = $request->session()->get('clinic');
        $files = $request->file('audios')[0];
        $fileName = explode('.', $files->getClientOriginalName());
            $files->move(public_path("files/{$type}"), $fileName[0] . "." . $fileName[1]);
            $media = Media::create([
                'clinic_id' => $clinic->getId(),
                'name' => $fileName[0],
                'url' => url("files/{$type}/" . $fileName[0] . "." . $fileName[1])
            ]);
        return response()->json($media);
    }
    public function remove(Media $media)
    {
        $media = Media::find($media);
        $media->delete();
        return response()->json();
    }
}
