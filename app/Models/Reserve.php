<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reserve extends Model
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
        'court_id',
        'club_id',
        'lights',
        'start_time',
        'end_time',
        'day',
        'user_id',
        'final_time',
        'start_Datetime',
    ];

    protected $table = 'reserves';
    protected $appends = ['pending_type'];

    public function courts(){
        return $this->belongsTo(Court::class,'court_id','id');
    }

    public function getPendingTypeAttribute() {
        return 'reserve';
    }
}
