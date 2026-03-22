<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoadMap extends Model
{
    protected $table = 'road_maps';

    public $timestamps = false;

    protected $fillable = [ 
        'title',
        'description',
        'image_path',
        'status',
        'created_by',
        'created_at',
        'updated_at',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
