<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Event;
use App\Models\Registration;
use App\Models\Review;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class CampusEventSeeder extends Seeder
{
    public function run(): void
    {
        $users = [];
        $names = ['Budi Santoso', 'Siti Aminah', 'Andi Pratama', 'Putri Ayu', 'Reza Rahardian'];
        $emails = ['budi.ump@gmail.com', 'siti.telkom@gmail.com', 'andi.unsoed@gmail.com', 'putri.saizu@gmail.com', 'reza.amikom@gmail.com'];
        
        for ($i = 0; $i < 5; $i++) {
            $users[] = User::create([
                'name' => $names[$i],
                'email' => $emails[$i],
                'password' => Hash::make('password123'),
            ]);
        }

        $eventData = [
            ['name' => 'Seminar AI Mada', 'desc' => 'Seminar kecerdasan buatan di Universitas Muhammadiyah Purwokerto.', 'loc' => 'Auditorium Ukhuwah Islamiyah UMP', 'status' => 'Selesai'],
            ['name' => 'Workshop Flutter', 'desc' => 'Pelatihan membuat aplikasi Flutter bareng Google Developer Student Clubs IT Telkom Purwokerto.', 'loc' => 'Lab Komputer IT Telkom Purwokerto', 'status' => 'Selesai'],
            ['name' => 'Unsoed Career Expo 2026', 'desc' => 'Pameran karir tahunan untuk mahasiswa dan alumni Unsoed.', 'loc' => 'Graha Widyatama Unsoed', 'status' => 'Mendatang'],
            ['name' => 'Kompetisi E-Sport Amikom', 'desc' => 'Turnamen Mobile Legends antar mahasiswa Amikom Purwokerto.', 'loc' => 'Aula Amikom Purwokerto', 'status' => 'Selesai'],
            ['name' => 'Kajian Rutin UIN Saizu', 'desc' => 'Kajian bulanan untuk mahasiswa UIN Prof. K.H. Saifuddin Zuhri.', 'loc' => 'Masjid Kampus UIN Saizu', 'status' => 'Mendatang'],
            ['name' => 'Pentas Seni Fakultas Ilmu Budaya', 'desc' => 'Penampilan seni dari mahasiswa FIB Unsoed.', 'loc' => 'Gedung Kesenian FIB Unsoed', 'status' => 'Mendatang'],
            ['name' => 'Lomba Debat Bahasa Inggris ITTP', 'desc' => 'Kompetisi debat antar prodi di IT Telkom Purwokerto.', 'loc' => 'Gedung Rektorat ITTP', 'status' => 'Selesai'],
            ['name' => 'Sosialisasi PKM 2026 UMP', 'desc' => 'Sosialisasi Program Kreativitas Mahasiswa oleh Kemahasiswaan UMP.', 'loc' => 'Gedung Rektorat UMP', 'status' => 'Selesai'],
            ['name' => 'Hackathon Banyumas 24 Jam', 'desc' => 'Lomba koding 24 jam untuk seluruh mahasiswa IT se-Banyumas.', 'loc' => 'Banyumas Creative Hub', 'status' => 'Mendatang'],
            ['name' => 'Bazar Kewirausahaan Mahasiswa', 'desc' => 'Bazar produk-produk kewirausahaan mahasiswa se-Banyumas.', 'loc' => 'Alun-alun Purwokerto', 'status' => 'Selesai'],
        ];

        $events = [];
        foreach ($eventData as $index => $data) {
            $events[] = Event::create([
                'name' => $data['name'],
                'description' => $data['desc'],
                'images' => null,
                'event_date' => Carbon::now()->addDays($data['status'] === 'Mendatang' ? 10 : -10)->addHours($index),
                'location' => $data['loc'],
                'quota' => 100 + ($index * 20),
                'status' => $data['status'],
            ]);
        }

        $reviewComments = ['Acara sangat bermanfaat!', 'Luar biasa, pematerinya keren.', 'Sangat inspiratif untuk mahasiswa.', 'Fasilitas sangat memadai.', 'Ditunggu event selanjutnya!'];
        
        foreach ($events as $event) {
            $randomUsers = collect($users)->random(2);
            
            foreach ($randomUsers as $user) {
                Registration::create([
                    'user_id' => $user->id,
                    'event_id' => $event->id,
                    'status' => 'Approved',
                ]);

                if ($event->status === 'Selesai' && $user->id === $randomUsers->first()->id) {
                    Review::create([
                        'user_id' => $user->id,
                        'event_id' => $event->id,
                        'rating' => rand(4, 5),
                        'comment' => collect($reviewComments)->random(),
                    ]);
                }
            }
        }
    }
}
