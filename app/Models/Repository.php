<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Repository extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'github_id',
        'owner',
        'url',
    ];

    public function commits() : HasMany
    {
        return $this->hasMany(Commits::class);
    }
}
