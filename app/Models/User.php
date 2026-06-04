<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * Class User
 * 
 * Model Eloquent untuk merepresentasikan data pengguna (mahasiswa, civitas akademika, atau administrator)
 * pada platform BanyuHub.space. Terintegrasi dengan fitur Laravel Sanctum untuk autentikasi API.
 */
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = ['name', 'email', 'password', 'role'];
    protected $hidden = ['password', 'remember_token'];

    /**
     * Mengatur casting tipe data atribut model User.
     * 
     * Memastikan data email_verified_at dikonversi ke objek datetime,
     * serta password di-hash secara otomatis menggunakan bcrypt/argon2 saat disimpan.
     * 
     * @return array<string, string> Daftar aturan casting atribut.
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Relasi satu-ke-banyak (One-to-Many) ke model Registration.
     * 
     * Menghubungkan pengguna dengan seluruh riwayat pendaftaran event (RSVP) yang pernah dilakukannya.
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
     * Menghubungkan pengguna dengan semua ulasan dan rating yang pernah mereka berikan pada event-event yang sudah selesai.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany Objek relasi HasMany ke model Review.
     */
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
}
