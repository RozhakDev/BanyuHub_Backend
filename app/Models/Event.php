<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Event
 * 
 * Model Eloquent untuk merepresentasikan kegiatan atau acara (seminar, workshop, kegiatan organisasi, dll.)
 * di BanyuHub.space. Berisi informasi nama event, deskripsi, tanggal, lokasi, kuota, dan status event.
 */
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

    protected $appends = ['image_urls'];

    /**
     * Accessor dinamis untuk mendapatkan absolute URL file gambar event.
     * 
     * Memproses array nama file gambar yang diunggah (`images`), lalu merubahnya
     * menjadi URL absolut lengkap mengarah ke folder public storage.
     * Ini memudahkan aplikasi mobile Flutter untuk langsung memuat gambar tanpa konfigurasi tambahan.
     * 
     * @return array<int, string> Daftar URL absolut gambar event.
     */
    public function getImageUrlsAttribute()
    {
        if (empty($this->images) || !is_array($this->images)) {
            return [];
        }
        
        return array_map(function ($path) {
            return asset('storage/' . $path);
        }, $this->images);
    }

    /**
     * Relasi satu-ke-banyak (One-to-Many) ke model Registration.
     * 
     * Menghubungkan event ini dengan seluruh pendaftaran peserta yang masuk untuk event ini.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany Objek relasi HasMany ke model Registration.
     */
    public function registrations()
    {
        return $this->hasMany(Registration::class);
    }

    /**
     * Relasi satu-ke-banyak (One-to-Many) ke model Review.
     * 
     * Menghubungkan event ini dengan seluruh ulasan (rating dan komentar) yang diberikan oleh para peserta.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany Objek relasi HasMany ke model Review.
     */
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
}
