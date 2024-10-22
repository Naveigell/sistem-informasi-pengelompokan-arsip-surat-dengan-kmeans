<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Kmeans extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'file_id',
        'cluster',
        'deleted_at',
    ];

    /**
     * Get the file that this kmeans belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function file()
    {
        return $this->belongsTo(File::class);
    }
}
