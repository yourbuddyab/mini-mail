<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProccesStatus extends Model
{
    use HasFactory;

    protected $fillable = [
        'campaign_id',
        'proccess',
        'failed',
        'total',
        'type'
    ];
}
