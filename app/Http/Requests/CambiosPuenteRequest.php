<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CambiosPuenteRequest extends FormRequest
{
    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'secuencia' => ['required', 'integer', 'min:1'],
            'items' => ['present', 'array', 'max:1000'],
            'items.*.codigo' => ['required', 'string', 'max:15'],
            'items.*.huella' => ['required', 'string', 'max:64'],
            'items.*.descripcion' => ['nullable', 'string', 'max:40'],
            'items.*.instancia' => ['nullable', 'integer'],
            'items.*.unidad' => ['nullable', 'string', 'max:3'],
            'items.*.precio1' => ['required', 'numeric'],
            'items.*.precio2' => ['required', 'numeric'],
            'items.*.precio3' => ['required', 'numeric'],
            'items.*.iva' => ['nullable', 'numeric', 'between:0,100'],
            'items.*.existencia' => ['required', 'numeric'],
            'items.*.activo' => ['required', 'boolean'],
            'eliminados' => ['present', 'array', 'max:1000'],
            'eliminados.*' => ['string', 'max:15'],
        ];
    }
}
