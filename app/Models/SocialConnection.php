<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SocialConnection extends Model
{
    protected $fillable = [
        'url', 
        'status', 
        'icon_image', 
        'created_by', 
        'updated_by'
    ];
    
    use HasFactory;

    /**
     * Get the full URL for the icon image.
     */
    public function getIconImageAttribute($value)
    {
        return $value ? asset($value) : null;
    }
}
