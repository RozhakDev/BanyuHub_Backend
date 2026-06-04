<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Registration;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

/**
 * Class RegistrationController
 * 
 * Controller ini menangani proses pendaftaran/partisipasi pengguna ke suatu event (RSVP).
 * Menyediakan fungsionalitas bagi pengguna terautentikasi untuk mendaftar ke event,
 * serta mengecek daftar event yang telah mereka ikuti beserta status approvalnya.
 */
class RegistrationController extends Controller
{
    #[OA\Post(
        path: '/api/register-event',
        tags: ['Registrations'],
        summary: 'Daftar ke sebuah event',
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'event_id', in: 'query', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 201, description: 'Registered successfully'),
            new OA\Response(response: 400, description: 'Kuota penuh atau sudah terdaftar'),
        ],
    )]
    /**
     * Mendaftarkan pengguna yang sedang login ke sebuah event.
     * 
     * Melakukan validasi ketat sebelum pendaftaran dibuat:
     * 1. Memastikan event ada di database.
     * 2. Memastikan status event bukan 'Selesai' atau 'Dibatalkan'.
     * 3. Memastikan kuota peserta event masih tersedia (lebih besar dari 0).
     * 4. Memastikan pengguna belum pernah mendaftar di event yang sama sebelumnya (mencegah duplikasi).
     * 
     * Jika semua validasi lolos, pendaftaran disimpan dengan status awal 'Pending' (dan memicu
     * model observer untuk otomatis mengurangi kuota event sebanyak 1).
     * 
     * @param \Illuminate\Http\Request $request Data request berisi parameter 'event_id'.
     * @return \Illuminate\Http\JsonResponse Respon sukses pendaftaran atau pesan error jika validasi gagal.
     */
    public function store(Request $request)
    {
        $request->validate(['event_id' => 'required|exists:events,id']);
        $user = $request->user();

        $event = \App\Models\Event::findOrFail($request->event_id);

        if (in_array($event->status, ['Selesai', 'Dibatalkan'])) {
            return response()->json(['message' => 'Pendaftaran ditutup karena event sudah selesai atau dibatalkan.'], 400);
        }

        if ($event->quota <= 0) {
            return response()->json(['message' => 'Mohon maaf, kuota event ini sudah penuh'], 400);
        }

        $exists = Registration::where('user_id', $user->id)
                            ->where('event_id', $request->event_id)
                            ->exists();

        if ($exists) {
            return response()->json(['message' => 'Anda sudah terdaftar di event ini'], 400);
        }

        $registration = Registration::create([
            'user_id' => $user->id,
            'event_id' => $event->id,
            'status' => 'Pending',
        ]);

        return response()->json(['message' => 'Berhasil mendaftar, menunggu approval', 'data' => $registration], 201);
    }

    #[OA\Get(
        path: '/api/my-registrations',
        tags: ['Registrations'],
        summary: 'Dapatkan status registrasi event pengguna',
        security: [['bearerAuth' => []]],
        responses: [
            new OA\Response(response: 200, description: 'Success'),
        ],
    )]
    /**
     * Mengambil daftar riwayat pendaftaran event milik pengguna yang sedang login.
     * 
     * Mengembalikan seluruh record pendaftaran yang dibuat oleh pengguna, lengkap dengan
     * relasi data detail event-nya, diurutkan dari pendaftaran yang terbaru.
     * 
     * @param \Illuminate\Http\Request $request Data request membawa informasi user terautentikasi.
     * @return \Illuminate\Http\JsonResponse Respon berisi daftar riwayat pendaftaran event milik user.
     */
    public function myRegistrations(Request $request)
    {
        $registrations = Registration::with('event')
                                    ->where('user_id', $request->user()->id)
                                    ->latest()
                                    ->get();

        return response()->json(['message' => 'Success', 'data' => $registrations], 200);
    }
}
