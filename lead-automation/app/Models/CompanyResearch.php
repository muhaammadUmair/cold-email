<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyResearch extends Model
{
    protected $table = 'company_research';

    protected $guarded = [];

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }
}