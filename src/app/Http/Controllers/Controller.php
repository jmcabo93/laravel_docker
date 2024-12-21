<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;


/**
 * @OA\Info(
 *     title="API Tienda",
 *     version="1.0.0",
 *     description="Esta es la documentación de la API que permite gestionar categorías, productos y ordenes en una tienda.",
 *     @OA\Contact(
 *         email="jmcabo93@tudominio.com"
 *     )
 * )
 */

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
}
