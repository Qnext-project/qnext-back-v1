<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Expertise extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'name',
        'is_title',
        'clinic_id',
        'media_id',
    ];

    protected $casts = [
        'is_title' => 'boolean',
    ];
    protected $appends = ['media_url'];

    public function clinic()
    {
        return $this->belongsTo(Clinic::class);
    }

    public function media()
    {
        return $this->belongsTo(Media::class);
    }

    public function getMediaUrlAttribute()
    {
        if(isset($this->media_id)) {
            return Media::find($this->media_id)->url;
        }
        return null;
    }
}
