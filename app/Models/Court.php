<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Court extends Model
{
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
        'surface',
    ];

    protected $table="courts";

    public function prices() {
        return $this->belongsToMany(Price::class, 'court_prices', 'court_id');
    }

    public function reserves() {
        return $this->hasMany(Reserve::class);
    }
}
