<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'duration',
        'status',
        'created_by'
    ];

    public function creator()
    {
        // 'created_by' is the foreign key in the courses table
        return $this->belongsTo(User::class, 'created_by');
    }
}
