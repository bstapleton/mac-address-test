<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Identifier extends Model
{
    protected $fillable = [
        'assignment',
    ];

    public function organisations(): BelongsToMany
    {
        return $this->belongsToMany(Organisation::class);
    }
}
