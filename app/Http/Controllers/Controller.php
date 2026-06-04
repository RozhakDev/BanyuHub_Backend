<?php

namespace App\Http\Controllers;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: '1.0.0',
    title: 'BanyuHub.space API Documentation',
    description: "Layanan API untuk sistem informasi event dan komunitas akademik lintas kampus di wilayah Banyumas yang mendukung autentikasi pengguna, manajemen event, registrasi peserta, ulasan event, serta integrasi aplikasi web dan mobile.\n\nDokumentasi API ini dirancang menggunakan standar REST API untuk memudahkan proses pengembangan dan integrasi sistem pada aplikasi mobile Flutter maupun aplikasi web Laravel.",
    contact: new OA\Contact(
        name: 'Tim Developer BanyuHub',
        email: 'developer@banyuhub.space'
    ),
)]
#[OA\Server(
    url: L5_SWAGGER_CONST_HOST,
    description: 'Primary API Server'
)]
#[OA\SecurityScheme(
    securityScheme: 'bearerAuth',
    type: 'http',
    scheme: 'bearer',
    bearerFormat: 'JWT',
    description: 'Masukkan Sanctum Token yang didapat dari endpoint Login.'
)]
#[OA\Tag(
    name: 'Authentication',
    description: 'Manajemen autentikasi mahasiswa (Login, Register, Logout).'
)]
#[OA\Tag(
    name: 'Events',
    description: 'Eksplorasi, pencarian, dan detail kegiatan atau organisasi kampus se-Banyumas.'
)]
#[OA\Tag(
    name: 'Registrations',
    description: 'Manajemen partisipasi mahasiswa (pendaftaran event dan riwayat RSVP).'
)]
#[OA\Tag(
    name: 'Reviews',
    description: 'Ulasan dan sistem rating komunitas terhadap event yang sudah selesai.'
)]
/**
 * Class Controller
 * 
 * Kelas dasar (base controller) untuk seluruh controller di aplikasi BanyuHub.space.
 * Berfungsi sebagai tempat konfigurasi metadata dan tag global untuk dokumentasi API OpenAPI (Swagger).
 */
abstract class Controller
{
    //
}
