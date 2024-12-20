<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
{
    /**
     * Determina si el usuario está autorizado a realizar esta solicitud.
     *
     * @return bool
     */
    public function authorize()
    {
        // Si la autenticación y autorización son necesarias, puedes devolver `true` o implementar la lógica aquí.
        return true;  // Cambia esto si necesitas alguna validación de acceso
    }

    /**
     * Obtén las reglas de validación que se aplicarán a la solicitud.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|image|max:2048',
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
            'name.required' => 'El nombre del producto es obligatorio.',
            'name.string' => 'El nombre del producto debe ser una cadena de texto.',
            'name.max' => 'El nombre del producto no puede exceder los 255 caracteres.',
            'description.string' => 'La descripción debe ser una cadena de texto.',
            'price.required' => 'El precio del producto es obligatorio.',
            'price.numeric' => 'El precio debe ser un número.',
            'price.min' => 'El precio debe ser al menos 0.',
            'stock.required' => 'La cantidad de stock es obligatoria.',
            'stock.integer' => 'El stock debe ser un número entero.',
            'stock.min' => 'El stock no puede ser menor a 0.',
            'category_id.required' => 'La categoría es obligatoria.',
            'category_id.exists' => 'La categoría seleccionada no existe.',
            'image.image' => 'El archivo debe ser una imagen válida.',
            'image.max' => 'La imagen no puede pesar más de 2MB.',
        ];
    }
}

