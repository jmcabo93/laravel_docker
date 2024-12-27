<?php

namespace App\Swagger;

use OpenApi\Annotations as OA;

    /**
     * @OA\Info(
     *     title="API Tienda",
     *     version="1.0.0",
     *     description="Esta es la documentación de la API que permite gestionar categorías, productos y órdenes en una tienda.",
     *     @OA\Contact(
     *         email="jmcabo93@gmail.com"
     *     )
     * )
     * @OA\SecurityScheme(
     *     securityScheme="bearerAuth",
     *     type="http",
     *     scheme="bearer",
     *     bearerFormat="JWT",
     *     description="Usa un token JWT en formato Bearer para autenticarte"
     * )

     * @OA\Post(
     *     path="/api/login",
     *     tags={"Autenticación"},
     *     summary="Login",
     *     description="Autenticación de usuario con email y password. Retorna un token Bearer para usar en solicitudes posteriores.",
     *     operationId="login",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(
     *                     property="email",
     *                     type="string",
     *                     example="user@example.com"
     *                 ),
     *                 @OA\Property(
     *                     property="password",
     *                     type="string",
     *                     example="password123"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Autenticación exitosa.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Autenticado exitosamente."
     *             ),
     *             @OA\Property(
     *                 property="token",
     *                 type="string",
     *                 example="Bearer {access_token}"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Credenciales incorrectas.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Credenciales incorrectas."
     *             )
     *         )
     *     )
     * )
     
     * @OA\Post(
     *     path="/api/logout",
     *     tags={"Autenticación"},
     *     summary="Logout",
     *     description="Cierra sesión y revoca todos los tokens de acceso del usuario autenticado.",
     *     operationId="logout",
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Cierre de sesión exitoso.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Successfully logged out"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="No autorizado.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="No autorizado"
     *             )
     *         )
     *     )
     * )
     */
    


class Auth
{
   
}
