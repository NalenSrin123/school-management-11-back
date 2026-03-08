<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class user_account extends Model
{
    use HasFactory;

    protected $primaryKey = 'UserID';

    protected $fillable = [
        'Username',
        'Email',
        'Password',
        'RoleID'
    ]; 
    public function role()
    {
        return $this->belongsTo(role::class, 'RoleID', 'RoleID');
    }
     public function events()
    {
        return $this->hasMany(event::class, 'CreatedBy', 'UserID');
    }
}
