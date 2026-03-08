<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    protected $fillable = [
        'title',
        'description',
        'created_by',
        'target_type',
        'target_id',
        'created_date',
        'expiry_date',
        'status',
        'priority',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
