<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

// Product validation request
// Solicitud de validación de producto
class ProductRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0'
        ];
    }
} 