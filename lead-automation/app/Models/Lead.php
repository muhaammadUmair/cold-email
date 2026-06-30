<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    protected $guarded = [];

    public function companyResearch()
    {
        return $this->hasOne(CompanyResearch::class);
    }

    public function emailLogs()
    {
        return $this->hasMany(EmailLog::class);
    }
}