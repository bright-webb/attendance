<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $fillable = [
        'name',
        'gitea_username',
        'system_id',
        'gender',
    ];

    public function attendanceRequests()
    {
        return $this->hasMany(AttendanceRequest::class);
    }
}
