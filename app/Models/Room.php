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
    ];

    public function media()
    {
        return $this->belongsTo(Media::class, 'media_id');
    }

    public function clinic()
    {
        return $this->belongsTo(Clinic::class, 'clinic_id');
    }


}
