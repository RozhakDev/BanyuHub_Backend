<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Event;
use App\Models\Review;
use OpenApi\Attributes as OA;

class ReviewController extends Controller
{
    #[OA\Get(
        path: '/api/events/{event}/reviews',
        tags: ['Reviews'],
        summary: 'Dapatkan semua ulasan untuk event tertentu',
        parameters: [
            new OA\Parameter(name: 'event', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Berhasil mendapatkan ulasan'),
        ]
    )]
    public function index(Event $event)
    {
        $reviews = $event->reviews()->with('user:id,name')->get();
        return response()->json(['message' => 'Berhasil mendapatkan ulasan', 'data' => $reviews], 200);
    }

    #[OA\Post(
        path: '/api/events/{event}/reviews',
        tags: ['Reviews'],
        summary: 'Tambahkan ulasan ke event',
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'event', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['rating'],
                properties: [
                    new OA\Property(property: 'rating', type: 'integer', example: 5),
                    new OA\Property(property: 'comment', type: 'string', example: 'Bagus banget!'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Ulasan berhasil ditambahkan'),
            new OA\Response(response: 400, description: 'Event belum selesai'),
            new OA\Response(response: 401, description: 'Tidak terautentikasi'),
            new OA\Response(response: 403, description: 'Tidak memiliki akses untuk mengulas'),
        ]
    )]
    public function store(Request $request, Event $event)
    {
        if ($event->status !== 'Selesai') {
            return response()->json(['message' => 'Event belum selesai, Anda tidak dapat memberikan ulasan pada event ini.'], 400);
        }

        $isRegisteredAndApproved = $event->registrations()
            ->where('user_id', $request->user()->id)
            ->where('status', 'Approved')
            ->exists();

        if (!$isRegisteredAndApproved) {
            return response()->json(['message' => 'Anda harus terdaftar dan disetujui (Approved) untuk dapat mengulas event ini.'], 403);
        }

        $existingReview = $event->reviews()->where('user_id', $request->user()->id)->exists();
        if ($existingReview) {
            return response()->json(['message' => 'Anda sudah memberikan ulasan untuk event ini. Ulasan hanya dapat diberikan satu kali.'], 400);
        }

        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000'
        ]);

        $review = $event->reviews()->create([
            'user_id' => $request->user()->id,
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        return response()->json(['message' => 'Ulasan berhasil ditambahkan', 'data' => $review], 201);
    }
}
