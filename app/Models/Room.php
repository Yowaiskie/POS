<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Room extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    public function sessions(): HasMany
    {
        return $this->hasMany(RoomSession::class);
    }

    public function activeSession(): HasOne
    {
        return $this->hasOne(RoomSession::class)->whereIn('status', ['active', 'warning', 'overtime']);
    }
}
