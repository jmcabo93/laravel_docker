<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderItemRequest extends FormRequest
{
    /**
     * Determine si el usuario está autorizado para realizar esta solicitud.
     *
     * @return bool
     */
    public function authorize()
    {
        return true; // Cambiar según las necesidades de autorización
    }

    /**
     * Obtener las reglas de validación para la solicitud.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'order_id' => 'required|exists:orders,id',
            'product_id' => 'required|exists:products,id', 
            'quantity' => 'required|integer', 
        ];
    }

    /**
     * Mensajes personalizados para las reglas de validación.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'order_id.required' => 'El campo "order_id" es obligatorio.',
            'order_id.exists' => 'La orden especificada no existe.',
            'product_id.required' => 'El campo "product_id" es obligatorio.',
            'product_id.exists' => 'El producto especificado no existe.',
            'quantity.required' => 'El campo "quantity" es obligatorio.',
            'quantity.integer' => 'La cantidad debe ser un número entero.',
        ];
    }
}
