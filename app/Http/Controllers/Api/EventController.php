<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

/**
 * Class EventController
 * 
 * Controller ini menangani penyajian data event untuk konsumsi API publik (terutama aplikasi mobile Flutter).
 * Menyediakan fungsionalitas pencarian event berdasarkan kata kunci, filter berdasarkan status event,
 * serta pengambilan detail lengkap suatu event.
 */
class EventController extends Controller
{
    #[OA\Get(
        path: '/api/events',
        tags: ['Events'],
        summary: 'Dapatkan semua event dengan fitur pencarian dan filter',
        parameters: [
            new OA\Parameter(name: 'search', in: 'query', required: false, description: 'Cari berdasarkan nama, deskripsi, atau lokasi', schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'status', in: 'query', required: false, description: 'Filter berdasarkan status', schema: new OA\Schema(type: 'string', enum: ['Mendatang', 'Sedang Berjalan', 'Selesai', 'Dibatalkan'])),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Success'),
        ],
    )]
    /**
     * Mengambil daftar event dengan dukungan pencarian, filter status, dan paginasi.
     * 
     * Memungkinkan client mencari event berdasarkan nama, deskripsi, atau lokasi,
     * serta melakukan filter berdasarkan status ('Mendatang', 'Sedang Berjalan', 'Selesai', 'Dibatalkan').
     * Hasil pencarian dibatasi menggunakan paginasi default sebanyak 10 item per halaman.
     * 
     * @param \Illuminate\Http\Request $request Data request berisi opsional query parameter 'search' dan 'status'.
     * @return \Illuminate\Http\JsonResponse Respon sukses berisi daftar event yang terpaginasi.
     */
    public function index(Request $request)
    {
        $query = Event::query();

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('location', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $events = $query->latest()->paginate(10);
        return response()->json(['message' => 'Success', 'data' => $events], 200);
    }

    #[OA\Get(
        path: '/api/events/{id}',
        tags: ['Events'],
        summary: 'Dapatkan detail event',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Success'),
            new OA\Response(response: 404, description: 'Not Found'),
        ],
    )]
    /**
     * Mengambil detail lengkap suatu event berdasarkan ID.
     * 
     * Mencari event yang sesuai dengan ID yang diberikan. Jika data ditemukan,
     * akan dikembalikan beserta respons sukses. Jika tidak ditemukan, mengembalikan status error 404.
     * 
     * @param int|string $id ID event yang dicari.
     * @return \Illuminate\Http\JsonResponse Respon berisi data detail event atau pesan error jika tidak ditemukan.
     */
    public function show($id)
    {
        $event = Event::find($id);
        if (!$event) return response()->json(['message' => 'Event tidak ditemukan'], 404);

        return response()->json(['message' => 'Success', 'data' => $event], 200);
    }
}
