<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class VideoGuideLine extends Model
{
    protected $table = 'video_guide_lines';

    public $timestamps = false;

    protected $fillable = [
        'title',
        'description',
        'file_path',
        'file_type',
        'uploaded_by',
        'upload_date',
        'visibility'
    ];

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
