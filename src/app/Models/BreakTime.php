<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class BreakTime extends Model
{
    use HasFactory;

    protected $table = 'break_times';

    protected $fillable = [
        'attendance_id',
        'break_in',
        'break_out',
    ];

    protected $casts = [
        'break_in'  => 'datetime',
        'break_out' => 'datetime',
    ];


    public function attendance()
    {
        return $this->belongsTo(Attendance::class);
    }


    public function getBreakStartAtAttribute()
    {
        return $this->break_in;
    }

    public function getBreakEndAtAttribute()
    {
        return $this->break_out;
    }

    public function durationMinutes(): int
    {
        if ($this->break_in && $this->break_out) {
            return Carbon::parse($this->break_in)->diffInMinutes(Carbon::parse($this->break_out));
        }
        return 0;
    }
function durationHours(): string
    {
        $minutes = $this->durationMinutes();
        $h = floor($minutes / 60);
        $m = $minutes % 60;
        return sprintf('%d:%02d', $h, $m);
    }
}
