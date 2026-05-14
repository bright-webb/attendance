<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceSession extends Model
{
    protected $fillable = [
        'session_code',
        'session_date',
    ];

    protected $casts = [
        'session_date' => 'date',
    ];

    public function attendanceRequests()
    {
        return $this->hasMany(AttendanceRequest::class);
    }
}
