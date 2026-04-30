<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlockedTime extends Model
{
    protected $guarded = [];

    public function counselor()
    {
        return $this->belongsTo(Counselor::class);
    }
}
