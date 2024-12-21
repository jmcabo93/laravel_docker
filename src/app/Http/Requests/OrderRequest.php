<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderRequest extends FormRequest
{
    public function authorize()
    {
        // Asegúrate de que el usuario está autenticado
        return true;
    }

    public function rules()
    {
        return [
            'user_id' => 'nullable|exists:users,id',
            'products' => 'required|array|min:1', // Aseguramos que sea un array con al menos un producto
            'products.*.product_id' => 'required|exists:products,id', // Validar que cada product_id exista
            'products.*.quantity' => 'required|integer|min:1', // Validar cantidad como entero positivo
        ];
    }

    public function messages()
    {
        return [
            'products.required' => 'Debe proporcionar al menos un producto.',
            'products.array' => 'El campo productos debe ser un arreglo.',
            'products.min' => 'Debe incluir al menos un producto en el pedido.',
            'products.*.product_id.required' => 'El ID del producto es obligatorio.',
            'products.*.product_id.exists' => 'El producto seleccionado no existe.',
            'products.*.quantity.required' => 'Debe especificar la cantidad del producto.',
            'products.*.quantity.integer' => 'La cantidad debe ser un número entero.',
            'products.*.quantity.min' => 'La cantidad debe ser al menos 1.',
        ];
    }
}