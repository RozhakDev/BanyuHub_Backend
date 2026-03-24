<?php

namespace App\Http\Controllers;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: '1.0.0',
    title: 'BanyuHub API Documentation',
    description: 'Dokumentasi REST API BanyuHub.space',
)]
#[OA\Server(
    url: L5_SWAGGER_CONST_HOST,
    description: 'Local API Server',
)]
#[OA\SecurityScheme(
    securityScheme: 'bearerAuth',
    type: 'http',
    scheme: 'bearer',
)]
abstract class Controller
{
    //
}
