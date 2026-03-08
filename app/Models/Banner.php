<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    protected $table = 'banners';

    public $timestamps = false;

    protected $fillable = [
        'title',
        'image_path',
        'created_by',
        'status',
        'created_date',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
