<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    protected $fillable = [
        'real_name',
        'upload_name',
        'deleted_at',
    ];

    /**
     * The URL of the file.
     *
     * @return string
     */
    public function getFileUrlAttribute()
    {
        return asset('storage/files/' . $this->attributes['upload_name']);
    }
}
