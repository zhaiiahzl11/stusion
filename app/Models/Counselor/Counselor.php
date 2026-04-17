<?php

namespace App\Models\Counselor;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Counselor extends Authenticatable
{
    use Notifiable;

    protected $guarded = [];
}
