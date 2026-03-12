<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class role extends Model
{
    use HasFactory;
    protected $primaryKey = 'RoleID';

     protected $fillable = [
        'Name'
    ];

    public function users()
    {
        return $this->hasMany(user_account::class, 'RoleID', 'RoleID');
    }
    
}
