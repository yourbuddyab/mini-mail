<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Campaign extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'contant', 'status', 'csv_file', 'scheduled_at', 'user_id'
    ];

    public function emails() {
        return $this->hasMany(Email::class);
    }
}
