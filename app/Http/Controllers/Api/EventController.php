<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use OpenApi\Attributes as OA;

class EventController extends Controller
{
    #[OA\Get(
        path: '/api/events',
        tags: ['Events'],
        summary: 'Dapatkan semua event',
        responses: [
            new OA\Response(response: 200, description: 'Success'),
        ],
    )]
    public function index()
    {
        $events = Event::latest()->paginate(10);
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
