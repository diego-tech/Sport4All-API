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
        'start_time',
        'end_time',
        'day',
        'user_id',
        'final_time',
        'start_Datetime',
    ];

    protected $table = 'reserves';

    public function courts(){
        return $this->belongsTo(Court::class,'court_id','id');
    }
}
