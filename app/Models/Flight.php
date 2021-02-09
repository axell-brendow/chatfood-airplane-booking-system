<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Flight extends Model
{
    use HasFactory, SoftDeletes, Traits\Uuid;
    protected $fillable = ['aircraft_id'];
    protected $dates = ['deleted_at'];
    protected $casts = [
        'id' => 'string'
    ];
    protected $keyType = 'string';
    public $incrementing = false;

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function aircraft()
    {
        return $this->belongsTo(Aircraft::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}
