<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    protected $table = 'locations';
    protected $fillable = [
        'name',
        'address',
        'link',
        'phone',
        'email',
        'status',
        'created_by',
        'created_date',
        'updated_date'
    ];

    public $timestamps = false;

    public function creator()
    {
        // return $this->belongsTo(User::class, 'created_by');
    }
}


