<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Email extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'email', 'status', 'campaign_id'
    ];

    public function campaign() {
        return $this->belongsToMany(Campaign::class);
    }
}
