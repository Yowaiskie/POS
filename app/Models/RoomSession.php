<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RoomSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_id',
        'status',
        'started_at',
        'ends_at',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function getTimerAttribute(): string
    {
        if (!$this->ends_at) {
            return '00:00:00';
        }

        $now = now();
        $isOvertime = $now->greaterThan($this->ends_at);
        $diffInSeconds = $this->ends_at->diffInSeconds($now);
        $formatted = $this->formatSeconds($diffInSeconds);

        return $isOvertime ? '+' . $formatted : $formatted;
    }

    private function formatSeconds(int $seconds): string
    {
        $hours = intdiv($seconds, 3600);
        $minutes = intdiv($seconds % 3600, 60);
        $remainingSeconds = $seconds % 60;

        return sprintf('%02d:%02d:%02d', $hours, $minutes, $remainingSeconds);
    }
}
