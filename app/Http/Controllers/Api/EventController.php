<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

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
    public function show($id)
    {
        $event = Event::find($id);
        if (!$event) return response()->json(['message' => 'Event tidak ditemukan'], 404);

        return response()->json(['message' => 'Success', 'data' => $event], 200);
    }
}
