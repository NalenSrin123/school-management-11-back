<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class event extends Model
{
    use HasFactory;
     protected $primaryKey = 'EventID';

    protected $fillable = [
        'Name',
        'Date',
        'Description',
        'CreatedBy',
        'CreatedDate'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'CreatedBy', 'id');
    }
}
