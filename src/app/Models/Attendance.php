<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'work_date',
        'clock_in',
        'clock_out',
        'status',
        'note',
    ];

    protected $casts = [
        'work_date' => 'date',
        'clock_in'  => 'datetime',
        'clock_out' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function breaks()
    {
        return $this->hasMany(BreakTime::class);
    }

    public function getClockInAtAttribute()
    {
        return $this->clock_in;
    }

    public function getClockOutAtAttribute()
    {
        return $this->clock_out;
    }


    public function totalBreakMinutes(): int
    {
        return $this->breaks->sum(function ($b) {
            return ($b->break_in && $b->break_out)
                ? Carbon::parse($b->break_in)->diffInMinutes(Carbon::parse($b->break_out))
                : 0;
        });
    }

    public function workedMinutes(): ?int
    {
        if (!$this->clock_in || !$this->clock_out) {
            return null;
        }

        return $this->clock_in->diffInMinutes($this->clock_out) - $this->totalBreakMinutes();
    }


    public function totalBreakHours(): string
    {
        $minutes = $this->totalBreakMinutes();
        $h = floor($minutes / 60);
        $m = $minutes % 60;
        return sprintf('%d:%02d', $h, $m);
    }

    public function workedHours(): ?string
    {
        $minutes = $this->workedMinutes();
        if ($minutes === null) {
            return null;
        }
        $h = floor($minutes / 60);
        $m = $minutes % 60;
        return sprintf('%d:%02d', $h, $m);
    }
}
