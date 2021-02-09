<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Seat extends Model
{
    use HasFactory, SoftDeletes, Traits\Uuid;
    protected $fillable = ['aircraft_id', 'name'];
    protected $dates = ['deleted_at'];
    protected $casts = [
        'id' => 'string'
    ];
    protected $keyType = 'string';
    public $incrementing = false;

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function aircraft()
    {
        return $this->belongsTo(Aircraft::class);
    }
}
