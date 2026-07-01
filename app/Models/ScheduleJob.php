<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScheduleJob extends Model
{
    protected $table = 'schedule_jobs';

    protected $guarded = [];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];
}
