<?php

// app/Models/WebsiteFile.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use App\Models\Website;

class WebsiteFile extends Model
{
    protected $fillable = [
        'website_id',
        'original_name',
        'file_name',
        'file_path'
    ];

    public function website()
    {
        return $this->belongsTo(Website::class);
    }

    public function getFileSizeAttribute()
    {
        if (Storage::disk('public')->exists($this->file_path)) {
            return Storage::disk('public')->size($this->file_path);
        }

        return 0;
    }
    
    public function getFileSizeKbAttribute()
    {
        return round($this->file_size / 1024, 2);
    }
}
