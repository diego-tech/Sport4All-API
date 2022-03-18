<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Matchs extends Model
{
    use \Backpack\CRUD\app\Models\Traits\CrudTrait;
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
        'day',
        'price_people',
        'start_time',
        'end_time',
        'final_time',
        'start_Datetime',
    ];

    protected $appends = ['pending_type'];

    public function users(){
        return $this->belongsToMany(User::class,'match_user','match_id');
    }

    public function getPendingTypeAttribute() {
        return 'match';
    }

    public function courts(){
        return $this->belongsToMany(Court::class);
    }

    public function club(){
        return $this->hasOne(Club::class);
    }
}
