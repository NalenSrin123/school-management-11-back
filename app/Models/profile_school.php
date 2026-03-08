<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProfileSchool extends Model
{
    use HasFactory;

    protected $fillable = [
        'image_path',
        'updated_by',
        'updated_date'
    ];

    /**
     * Get the user who last updated the profile.
     */
    public function editor()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
