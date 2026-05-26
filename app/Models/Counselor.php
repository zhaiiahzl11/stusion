<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Counselor extends Authenticatable
{
    use Notifiable;
    protected $guarded = [];
    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function availabilityRequests()
    {
        return $this->hasMany(AvailabilityRequest::class);
    }

    public function counselingSessions()
    {
        return $this->hasMany(CounselingSession::class);
    }

    public function sessionRequests()
    {
        return $this->hasMany(SessionRequest::class);
    }

    public function blockedTimes()
    {
        return $this->hasMany(BlockedTime::class);
    }
}
