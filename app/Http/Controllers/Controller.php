<?php

namespace App\Http\Controllers;

/**
 * @OA\Info(
 *      version="1.0.0",
 *      title="Postal Registration API",
 *      description="Postal Registration - Laravel API Documentation",
 * )
 *
 * @OA\SecurityScheme(
 *   securityScheme="bearerAuth",
 *   type="http",
 *   scheme="bearer"
 * )
 *
 * @OA\Server(url=L5_SWAGGER_CONST_HOST)
 **/
abstract class Controller
{
    //
}
