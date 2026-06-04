<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'images',
        'event_date',
        'location',
        'quota',
        'status',
    ];

    protected $casts = [
        'event_date' => 'datetime',
        'images' => 'array',
    ];

    public function registrations()
    {
        return $this->hasMany(Registration::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
}
