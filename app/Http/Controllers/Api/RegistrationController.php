<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Registration;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

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
        ],
    )]
    public function store(Request $request)
    {
        $request->validate(['event_id' => 'required|exists:events,id']);
        $user = $request->user();

        $exists = Registration::where('user_id', $user->id)
                            ->where('event_id', $request->event_id)
                            ->exists();

        if ($exists) {
            return response()->json(['message' => 'Anda sudah terdaftar di event ini'], 400);
        }

        $registration = Registration::create([
            'user_id' => $user->id,
            'event_id' => $request->event_id,
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
    public function myRegistrations(Request $request)
    {
        $registrations = Registration::with('event')
                                    ->where('user_id', $request->user()->id)
                                    ->latest()
                                    ->get();

        return response()->json(['message' => 'Success', 'data' => $registrations], 200);
    }
}
