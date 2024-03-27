<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;
class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'clinic_id',
        'username',
        'password',
        'role',
        'acl',
        'current_turn_number',
        'current_turn_time',
        'room_id',
        'media_id',
        'title_id',
        'expertise_id',
        'is_super_admin',
        'fpass',
        'doctor_id',
        'socket_id'
    ];
    protected $appends = ['expertise_audio_url', 'title_name', 'expertise_name', 'doc_info'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'password' => 'hashed',
        'acl' => 'json',
    ];

    public function clinic()
    {
        return $this->belongsTo(Clinic::class, 'clinic_id');
    }

    public function room()
    {
        return $this->belongsTo(Room::class, 'room_id');
    }

    public function media()
    {
        return $this->belongsTo(Media::class, 'media_id');
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function getExpertiseAudioUrlAttribute()
    {
        $fullAudio = [];
        if($this->title_id){
            $title = Expertise::find($this->title_id);
        }
        if($this->expertise_id){
            $expertise = Expertise::find($this->expertise_id);
        }
        if(isset($title)){
            $titleAudio = Media::find($title->media_id);
        }
        if(isset($expertise)){
            $expertiseAudio = Media::find($expertise->media_id);
        }
        if(isset($titleAudio)){
            $fullAudio[] = $titleAudio->url;
        }
        if(isset($expertiseAudio)){
            $fullAudio[] = $expertiseAudio->url;
        }
        return $fullAudio;
    }

    public function getTitleNameAttribute()
    {
        if($this->title_id){
            return Expertise::find($this->title_id)->name;
        }
        return null;
    }

    public function getExpertiseNameAttribute()
    {
        if($this->expertise_id){
            return Expertise::find($this->expertise_id)->name;
        }
        return null;
    }

    public function getDocInfoAttribute()
    {
        $docInfo = null;
        if($this->doctor_id){
            $doc = User::find($this->doctor_id);
            $docName = $doc?->first_name.' '.$doc?->last_name;
            $docId = $doc?->id;
            $docTitle = Expertise::find($doc?->title_id)?->name;
            $docExp = Expertise::find($doc?->expertise_id)?->name;
            $docRoom = Room::find($this->room_id)?->number;
            $docInfo = [
                'name' => $docName,
                'id' => $docId,
                'title' => $docTitle,
                'expertise' => $docExp,
                'room' => $docRoom
            ];
        }
        return $docInfo;
    }
}
