<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class About extends Model
{


    protected $fillable = [
        'title',
        'description',
        'content',
        'created_by',
        'updated_by',
        'created_date',
        'updated_date',
        'status'
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
