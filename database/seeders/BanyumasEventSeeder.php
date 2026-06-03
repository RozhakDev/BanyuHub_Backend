<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Event;
use App\Models\User;
use App\Models\Registration;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class BanyumasEventSeeder extends Seeder
{
    public function run(): void
    {
        $user1 = User::create([
            'name' => 'Budi Santoso',
            'email' => 'budi@gmail.com',
            'password' => Hash::make('Budi123'),
            'role' => 'user',
        ]);

        $user2 = User::create([
            'name' => 'Siti Aminah',
            'email' => 'siti@gmail.com',
            'password' => Hash::make('Siti123'),
            'role' => 'user',
        ]);

        $event1 = Event::create([
            'name' => 'Festival Baturraden 2026',
            'description' => 'Festival tahunan kesenian dan budaya khas Banyumas di kawasan wisata Baturraden. Menampilkan Ebeg, Kentongan, dan tarian tradisional.',
            'event_date' => Carbon::now()->addDays(10),
            'location' => 'Kawasan Wisata Baturraden, Purwokerto',
            'quota' => 500,
        ]);

        $event2 = Event::create([
            'name' => 'Banyumas Creative Market',
            'description' => 'Pasar kreatif anak muda Purwokerto. Menjajakan produk UMKM lokal, kuliner khas seperti Mendoan, dan kerajinan tangan.',
            'event_date' => Carbon::now()->addDays(5),
            'location' => 'Alun-Alun Purwokerto',
            'quota' => 1000,
        ]);

        $event3 = Event::create([
            'name' => 'Gowes Bareng Keliling Purwokerto',
            'description' => 'Acara sepeda sehat keliling kota Purwokerto dimulai dari Menara Teratai sampai GOR Satria.',
            'event_date' => Carbon::now()->addDays(2),
            'location' => 'Menara Teratai Purwokerto',
            'quota' => 200,
        ]);

        Registration::create([
            'user_id' => $user1->id,
            'event_id' => $event1->id,
            'status' => 'Pending',
        ]);

        Registration::create([
            'user_id' => $user1->id,
            'event_id' => $event2->id,
            'status' => 'Approved',
        ]);

        Registration::create([
            'user_id' => $user2->id,
            'event_id' => $event1->id,
            'status' => 'Approved',
        ]);
    }
}
