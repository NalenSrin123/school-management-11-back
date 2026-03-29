<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    use HasFactory;

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