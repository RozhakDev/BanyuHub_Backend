<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Review
 * 
 * Model Eloquent untuk menyimpan data ulasan (skor rating 1-5 dan feedback tertulis)
 * dari peserta yang dikirimkan setelah status event dinyatakan 'Selesai'.
 */
class Review extends Model
{
    protected $fillable = ['user_id', 'event_id', 'rating', 'comment'];

    /**
     * Relasi banyak-ke-satu (Belongs-to) ke model User.
     * 
     * Mendapatkan data pengguna (peserta) yang menulis ulasan ini.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo Objek relasi BelongsTo ke model User.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi banyak-ke-satu (Belongs-to) ke model Event.
     * 
     * Mendapatkan data event yang diberi ulasan.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo Objek relasi BelongsTo ke model Event.
     */
    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}
