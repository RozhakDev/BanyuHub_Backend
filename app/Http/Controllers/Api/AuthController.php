<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use OpenApi\Attributes as OA;

/**
 * Class AuthController
 * 
 * Controller ini menangani proses autentikasi pengguna aplikasi BanyuHub.space.
 * Menyediakan fungsionalitas registrasi mahasiswa baru, login dengan penerbitan token Sanctum,
 * pengecekan profil pengguna (me), serta proses logout untuk menghapus token akses.
 */
class AuthController extends Controller
{
    #[OA\Post(
        path: '/api/register',
        tags: ['Authentication'],
        summary: 'Register pengguna baru',
        parameters: [
            new OA\Parameter(name: 'name', in: 'query', required: true, schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'email', in: 'query', required: true, schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'password', in: 'query', required: true, schema: new OA\Schema(type: 'string')),
        ],
        responses: [
            new OA\Response(response: 201, description: 'User registered successfully'),
        ],
    )]
    /**
     * Mendaftarkan pengguna (mahasiswa/civitas) baru ke sistem.
     * 
     * Proses ini memvalidasi input data nama, email unik, dan password minimal 6 karakter.
     * Setelah data disimpan dengan password terenkripsi, sistem langsung menerbitkan
     * token akses API (Sanctum) agar pengguna baru bisa langsung terautentikasi.
     * 
     * @param \Illuminate\Http\Request $request Data request berisi name, email, dan password.
     * @return \Illuminate\Http\JsonResponse Respon sukses registrasi beserta data pengguna dan token akses.
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'user',
        ]);

        $token = $user->createToken('mobile_token')->plainTextToken;

        return response()->json([
            'message' => 'Registrasi berhasil',
            'data' => $user,
            'token' => $token
        ], 201);
    }

    #[OA\Post(
        path: '/api/login',
        tags: ['Authentication'],
        summary: 'Login pengguna',
        parameters: [
            new OA\Parameter(name: 'email', in: 'query', required: true, schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'password', in: 'query', required: true, schema: new OA\Schema(type: 'string')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Login successful'),
        ],
    )]
    /**
     * Melakukan proses login pengguna.
     * 
     * Memverifikasi kecocokan email dan password yang dikirimkan.
     * Jika sukses, token akses API (Sanctum) baru akan diterbitkan untuk sesi pengguna ini.
     * Jika gagal, sistem akan mengembalikan pengecualian validasi berupa pesan kesalahan kredensial.
     * 
     * @param \Illuminate\Http\Request $request Data request berisi email dan password.
     * @return \Illuminate\Http\JsonResponse Respon sukses login beserta data profil dan token akses.
     * @throws \Illuminate\Validation\ValidationException Jika kredensial yang dimasukkan salah.
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Kredensial tidak valid.'],
            ]);
        }

        $token = $user->createToken('mobile_token')->plainTextToken;

        return response()->json([
            'message' => 'Login berhasil',
            'data' => $user,
            'token' => $token
        ], 200);
    }

    #[OA\Get(
        path: '/api/user',
        tags: ['User'],
        summary: 'Get authenticated user',
        security: [['bearerAuth' => []]],
        responses: [
            new OA\Response(response: 200, description: 'Authenticated user data'),
        ],
    )]
    /**
     * Mengambil informasi profil pengguna yang sedang login.
     * 
     * Endpoint ini memerlukan token akses Bearer yang valid. Data yang dikembalikan
     * berupa objek detail profil pengguna (ID, nama, email, role, dan penanda verifikasi).
     * 
     * @param \Illuminate\Http\Request $request Data request yang membawa informasi user terautentikasi.
     * @return \App\Models\User Objek model user yang saat ini sedang login.
     */
    public function me(Request $request)
    {
        return $request->user();
    }

    #[OA\Post(
        path: '/api/logout',
        tags: ['Authentication'],
        summary: 'Logout pengguna',
        security: [['bearerAuth' => []]],
        responses: [
            new OA\Response(response: 200, description: 'Logout successful'),
        ],
    )]
    /**
     * Melakukan proses logout pengguna.
     * 
     * Mencabut dan menghapus token akses API (Sanctum) yang sedang aktif digunakan,
     * sehingga sesi autentikasi pengguna tersebut diakhiri dan tidak bisa lagi mengakses endpoint terproteksi.
     * 
     * @param \Illuminate\Http\Request $request Data request membawa informasi user terautentikasi.
     * @return \Illuminate\Http\JsonResponse Respon sukses yang menyatakan logout berhasil.
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logout berhasil'], 200);
    }
}
