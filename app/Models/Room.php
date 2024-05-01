<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Room extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'name',
        'number',
        'media_id',
        'clinic_id',
        'floor_id'
    ];

    protected $appends = ['floor_name'];
    public function media()
    {
        return $this->belongsTo(Media::class, 'media_id');
    }

    public function clinic()
    {
        return $this->belongsTo(Clinic::class, 'clinic_id');
    }

    public function floors()
    {
        return $this->belongsTo(Floor::class, 'floor_id');
    }

    public function users(){
        return $this->hasMany(User::class, 'room_id', 'id');
    }

    public function getFloorNameAttribute(){

	return $this->floor_id ? Floor::find($this->floor_id)?->name : null;
}
}
