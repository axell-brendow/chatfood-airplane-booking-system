<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Booking extends Model
{
    use HasFactory, SoftDeletes, Traits\Uuid;
    protected $fillable = ['user_id', 'flight_id', 'seat_id'];
    protected $dates = ['deleted_at'];
    protected $casts = [
        'id' => 'string'
    ];
    protected $keyType = 'string';
    public $incrementing = false;

    public function seat()
    {
        return $this->belongsTo(Seat::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function flight()
    {
        return $this->belongsTo(Flight::class);
    }
}
