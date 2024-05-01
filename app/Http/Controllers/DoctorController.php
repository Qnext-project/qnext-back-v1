<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChangeTurnRequest;
use App\Http\Requests\CreateDoctorRequest;
use App\Http\Requests\EditDoctorRequest;
use App\Models\Floor;
use App\Models\Room;
use App\Models\User;
use App\Models\Media;
use App\Models\Expertise;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;

class DoctorController extends Controller
{
    //
    public function create(CreateDoctorRequest $request)
    {
        $clinic = $request->session()->get('clinic');
        $request = $request->validated();
        if (isset($request['password'])) {
            $request['fpass'] = $request['password'];
            $request['password'] = Hash::make($request['password']);
            $request['role'] = 'admin';
        }
        $request = [
            ...$request,
            'clinic_id' => $clinic->getId()
        ];
        $user = User::create($request);
        return response()->json($user);
    }
    public function edit(User $user, EditDoctorRequest $request)
    {
        $request = $request->validated();
        if (isset($request['password'])) {
            $request['fpass'] = $request['password'];
            $request['password'] = Hash::make($request['password']);
        }
        $user = User::find($user->id);
        $user->update($request);
        return response()->json($user);
    }

    public function index(Request $request)
    {
        $doctors = User::where('role', 'doctor')->get();
        return response()->json($doctors);
    }

    public function listAd(Request $request)
    {
        $doctors = User::where('role', 'admin')->get();
        return response()->json($doctors);
    }

    public function show(User $user)
    {
        return response()->json($user);
    }

    public function adminList(Floor $floor)
    {
	$rooms = Room::where('floor_id', $floor->id)->has('users')->get();
	$doctors = [];
	foreach ($rooms as $room) {
        $user = User::where([['role', '=', 'admin'], ['room_id', '=', $room->id]])->whereNotNull('current_turn_number')->first();
        if($user){
            $doctors[] = $user;
        }
    }
        return response()->json($doctors);
    }

    public function remove(int $user)
    {
        $user = User::find($user);
        $user->update(['username' => $user->username . time()]);
        $user->delete();
        return response()->noContent();
    }

    public function loginAsDoctor(User $user, Room $room)
    {
        $alreadyLoggedIn = User::where([
            ['doctor_id', $user->id],
            ['room_id', $room->id],
        ])->exists();
        if ($alreadyLoggedIn) {
            abort(400, 'not allowed, a doctor already logged in');
        } else {
            $userToUp = User::find(auth()->user()->id);
            $userToUp->update(['doctor_id' => $user->id, 'room_id' => $room->id, 'current_turn_number' => 0]);
            return response()->json(User::find($userToUp->id), 200);
        }
    }
    public function updateTurn(ChangeTurnRequest $request)
    {
        $request = $request->validated();
        $userUp = User::find(auth()->user()->id);
        $userUp->update(
            [
                'current_turn_number' => $request['turn_number'],
                'current_turn_time' => $request['turn_time'] ?? null
            ]
        );
        return response()->json($userUp, 200);
    }

    public function purgeAll()
    {
        $user = User::find(auth()->user()->id);
        $user->update([
            'doctor_id' => null,
            'room_id' => null,
            'current_turn_number' => null,
            'current_turn_time' => null
        ]);
        return response()->noContent();
    }

	public function purgeUser(Request $request)
{
	$user = $request['user_id'];
$user = User::find($user);
        $user->update([
            'doctor_id' => null,
            'room_id' => null,
            'current_turn_number' => null,
            'current_turn_time' => null
        ]);
return response()->noContent();
}


    public function getDocVoice(Request $request)
    {
        $userUp = User::find($request['id']);
        $attachedDoc = User::find($userUp?->doctor_id);
        $expTitle = Expertise::find($attachedDoc?->title_id);
        $exp = Expertise::find($attachedDoc?->expertise_id);
        /* $room = Room::where(
            [[
                'id', '=', $userUp?->doc_info['room']
            ],
            ]
        ); */
        $room = Room::find($request['room_id']);
        if ($userUp?->current_turn_number == 0 || $userUp?->current_turn_number == null) {
            return response()->json(null, 200);
        }
        $ctn = strval($userUp?->current_turn_number);
        $roomNumber = strval($room?->number);
        $audios = [];
       // $audios['num'] = Media::where('name', 'LIKE', $ctn)->first();
        $audios['room_num'] = Media::where('name', 'LIKE', $roomNumber)->first();
        $audios['num'] = Media::where('name', 'LIKE', '%Shomare%')->first();
        $audios['room'] = Media::where('name', 'LIKE', '%به اتاقِ%')->first();
        //$audios['room_num'] = Media::find($room?->media_id) ?? Media::where('name', 'LIKE', "%{$userUp->doc_info['room']}%")->first();
        $audios['title'] = Media::find($expTitle?->media_id);
        $audios['expertise'] = Media::find($exp?->media_id);
        $num = intval($userUp->current_turn_number);
        $numLen = strlen((string) $num);
        if ($numLen == 1) {
   //         $audios['numbers'][0] = Media::where('name', 'LIKE', "%{$num}%")->first();
            $audios['numbers'][0] = Media::where('name', 'LIKE', "{$num}")->first();

        } else if ($numLen == 2 && $num <= 20) {
            $audios['numbers'][0] = Media::where('name', 'LIKE', "%{$num}%")->first();
        } else if ($numLen == 2 && $num > 20) {
            $firstHalf = substr($num, 0, 1);

            $secondHalf = substr($num, 1, 2);
            if ($secondHalf !== "0") {
                $audios['numbers'][0] = Media::where('name', 'LIKE', "%{$firstHalf}0o%")->first();
                $audios['numbers'][1] = Media::where('name', 'LIKE', "%{$secondHalf}%")->first();
            } else {
                $audios['numbers'][0] = Media::where('name', 'LIKE', "%{$firstHalf}0%")->first();
            }
        } else if ($numLen == 3 && $num <= 999) {
            $firstHalf = substr($num, 0, 1);
            $secondHalf = substr($num, 1, 2);
            if ($secondHalf == "00") {
                $audios['numbers'][0] = Media::where('name', 'LIKE', "%{$firstHalf}00%")->first();
            } else {
                $audios['numbers'][0] = Media::where('name', 'LIKE', "%{$firstHalf}00o%")->first();
                if (substr($secondHalf, 0, 1) == "0") {
                    if (substr($num, 1, -1) == "0") {
                        $fifth = substr($num, 2);

                        $audios['numbers'][1] = Media::where('name', 'LIKE', "%{$fifth}%")->first();
                    } else {
                        $audios['numbers'][1] = Media::where('name', 'LIKE', "%{$secondHalf}%")->first();
                    }
                } else {
                    $secondHalf = (int) $secondHalf;
                    if ($secondHalf <= 20) {
                        $audios['numbers'][1] = Media::where('name', 'LIKE', "%{$secondHalf}%")->first();
                    } else {
                        $thirdHalf = substr($secondHalf, 0, 1);
                        $fourthHalf = substr($secondHalf, 1, 2);

                        $audios['numbers'][1] = Media::where('name', 'LIKE', "%{$thirdHalf}0o%")->first();
                        if ($fourthHalf != "0") {
                            $audios['numbers'][2] = Media::where('name', 'LIKE', "%{$fourthHalf}%")->first();
                        }
                    }
                }

            }
        } else if ($numLen == 4 && $num >= 1000 && $num <= 1999) {
            $firstDigit = substr($num, 0, 1);
            $lastThreeDigits = substr($num, 1);

            if ($lastThreeDigits == "000") {
                $audios['numbers'][0] = Media::where('name', 'LIKE', "%{$firstDigit}000%")->first();
            } else {
                $audios['numbers'][0] = Media::where('name', 'LIKE', "%{$firstDigit}000o%")->first();

                // Handle the tens and ones
                $tensAndOnes = substr($lastThreeDigits, 1);
                if ($tensAndOnes == "00") {
                    // Handle cases like 1010, 1020, etc.
                    $audios['numbers'][1] = Media::where('name', 'LIKE', "%{$lastThreeDigits}%")->first();
                } else {
                    $secondHalf = substr($tensAndOnes, 0, 2);

                    // Similar logic as before for handling the second half
                    if (substr($secondHalf, 0, 1) == "0") {
                        if (substr($tensAndOnes, 1, 1) == "0") {
                            $thirdPart = substr($tensAndOnes, 2);
                            $audios['numbers'][1] = Media::where('name', 'LIKE', "%{$thirdPart}%")->first();
                        } else {
                            $audios['numbers'][1] = Media::where('name', 'LIKE', "%{$secondHalf}%")->first();
                        }
                    } else {
                        $secondHalf = (int) $secondHalf;
                        if ($secondHalf <= 20) {
                            $audios['numbers'][1] = Media::where('name', 'LIKE', "%{$secondHalf}%")->first();
                        } else {
                            $thirdPart = substr($secondHalf, 0, 1);
                            $fourthPart = substr($secondHalf, 1, 2);

                            $audios['numbers'][1] = Media::where('name', 'LIKE', "%{$thirdPart}0o%")->first();
                            if ($fourthPart != "0") {
                                $audios['numbers'][2] = Media::where('name', 'LIKE', "%{$fourthPart}%")->first();
                            }
                        }
                    }
                }
            }
        }



        return response()->json($audios, 200);
    }

    public function updateSocket(Request $request)
    {
        $socketId = $request['socketId'];
        $username = $request['username'];
        User::where('username', $username)->update(['socket_id' => $socketId]);
        return response()->json(null, 200);
    }
}
