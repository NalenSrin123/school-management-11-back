<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchoolLogo extends Model
{
    use HasFactory;

     protected $table = 'school_logos';

    protected $fillable = [
        'logo'
    ];
}
