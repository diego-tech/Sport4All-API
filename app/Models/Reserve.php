<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reserve extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'QR',
        'court_id',
        'lights',
        'start_dateTime',
        'end_dateTime',
        'user_id',
    ];

    protected $table = 'reserves';
}
