<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Aircraft extends Model
{
    use HasFactory, SoftDeletes, Traits\Uuid;

    public const TYPE_SHORT_RANGE = 0;

    protected $fillable = ['aircraft_type', 'seats_count', 'rows', 'row_arrangement'];
    protected $dates = ['deleted_at'];
    protected $casts = [
        'id' => 'string'
    ];
    protected $keyType = 'string';
    public $incrementing = false;

    public function flights()
    {
        return $this->hasMany(Flight::class);
    }

    public function seats()
    {
        return $this->hasMany(Seat::class);
    }
}
