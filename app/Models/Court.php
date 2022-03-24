<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Court extends Model
{
    use \Backpack\CRUD\app\Models\Traits\CrudTrait;
    use HasFactory;

     /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'club_id',
        'type',
        'price',
        'sport',
        'surfaces',
    ];

    protected $table="courts";

    public function prices() {
        return $this->belongsToMany(Price::class, 'court_prices', 'court_id');
    }

    public function reserves() {
        return $this->hasMany(Reserve::class);
    }

    public function club()
    {
        return $this->hasOne(Club::class,'id');
    }
}
