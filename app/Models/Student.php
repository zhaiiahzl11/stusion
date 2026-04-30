<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Student extends Authenticatable
{
    use Notifiable;
    protected $guarded = [];
    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function sessionRequests()
    {
        return $this->hasMany(SessionRequest::class);
    }

    public function counselingSessions()
    {
        return $this->hasMany(CounselingSession::class);
    }
}
