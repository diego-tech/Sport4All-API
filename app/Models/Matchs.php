<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Matchs extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'QR',
        'club_id',
        'court_id',
        'lights',
        'price_people',
        'start_dateTime',
        'end_dateTime',
    ];


}
