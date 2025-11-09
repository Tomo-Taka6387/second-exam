<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Models\Application;

class AttendanceRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'date',
        'clock_in',
        'clock_out',
        'break_in',
        'break_out',
        'break2_in',
        'break2_out',
        'total_time',
        'total_break_time',
        'comment',
    ];

    protected $casts = [
        'date'        => 'date',
        'clock_in'    => 'datetime:H:i',
        'clock_out'   => 'datetime:H:i',
        'break_in'    => 'datetime:H:i',
        'break_out'   => 'datetime:H:i',
        'break2_in'   => 'datetime:H:i',
        'break2_out'  => 'datetime:H:i',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function applications()
    {
        return $this->hasMany(Application::class);
    }

    public function calculateTotals()
    {
        $breakMinutes = 0;

        if ($this->break_in && $this->break_out) {
            $breakMinutes += Carbon::parse($this->break_out)
                ->diffInMinutes(Carbon::parse($this->break_in));
        }

        if ($this->break2_in && $this->break2_out) {
            $breakMinutes += Carbon::parse($this->break2_out)
                ->diffInMinutes(Carbon::parse($this->break2_in));
        }

        $workMinutes = 0;
        if ($this->clock_in && $this->clock_out) {
            $workMinutes = Carbon::parse($this->clock_out)
                ->diffInMinutes(Carbon::parse($this->clock_in));
        }

        $this->total_break_time = $this->formatMinutes($breakMinutes);
        $this->total_time = $this->formatMinutes(max(0, $workMinutes - $breakMinutes));
    }

    private function formatMinutes($minutes)
    {
        $h = floor($minutes / 60);
        $m = $minutes % 60;
        return sprintf('%02d:%02d', $h, $m);
    }

    protected static function booted()
    {
        static::saving(function ($attendance) {
            $attendance->calculateTotals();
        });
    }

    public function application()
    {
        return $this->hasOne(Application::class, 'attendance_record_id');
    }


    public function getStatusAttribute()
    {
        if ($this->clock_out) {
            return '退勤済';
        }


        if ($this->break2_in && !$this->break2_out) {
            return '休憩中';
        }

        if ($this->break_in && !$this->break_out) {
            return '休憩中';
        }

        if ($this->clock_in) {
            return '出勤中';
        }

        return '勤務外';
    }
}
