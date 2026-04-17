<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Counselor extends Authenticatable
{
    use Notifiable;
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
}
