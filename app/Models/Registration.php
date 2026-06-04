<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Registration
 * 
 * Model Eloquent untuk mencatat partisipasi pendaftaran pengguna ke suatu event (RSVP).
 * Berfungsi memetakan pendaftaran beserta status approvalnya ('Pending', 'Approved', 'Rejected').
 */
class Registration extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'event_id',
        'status',
    ];

    /**
     * Relasi banyak-ke-satu (Belongs-to) ke model User.
     * 
     * Mendapatkan data pengguna (peserta) yang melakukan pendaftaran ini.
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
     * Mendapatkan data event yang didaftar oleh pengguna pada pendaftaran ini.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo Objek relasi BelongsTo ke model Event.
     */
    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * Metode boot model untuk mendefinisikan Event Lifecycle Listener (Observer).
     * 
     * Berisi logic otomatis penyesuaian kuota event saat terjadi tindakan CRUD pendaftaran:
     * 1. Saat Pendaftaran Dibuat (`created`): Jika statusnya bukan 'Rejected', kurangi kuota event sebanyak 1.
     * 2. Saat Pendaftaran Diubah (`updated`): Jika status berubah menjadi 'Rejected', kembalikan/tambahkan kuota event (+1).
     *    Jika status awal ditolak lalu diubah ke status lain, potong kuota event (-1).
     * 3. Saat Pendaftaran Dihapus (`deleted`): Jika status awal bukan 'Rejected', kembalikan/tambahkan kuota event (+1).
     * 
     * Ini menjamin kuota event selalu sinkron baik dari registrasi via API mobile maupun manajemen dashboard admin.
     * 
     * @return void
     */
    protected static function booted()
    {
        static::created(function ($registration) {
            if ($registration->status !== 'Rejected') {
                $registration->event()->decrement('quota');
            }
        });

        static::updated(function ($registration) {
            if ($registration->isDirty('status')) {
                if ($registration->status === 'Rejected') {
                    $registration->event()->increment('quota');
                }
                if ($registration->getOriginal('status') === 'Rejected' && $registration->status !== 'Rejected') {
                    $registration->event()->decrement('quota');
                }
            }
        });

        static::deleted(function ($registration) {
            if ($registration->status !== 'Rejected') {
                $registration->event()->increment('quota');
            }
        });
    }
}
