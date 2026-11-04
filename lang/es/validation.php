<?php

return [
    'required' => 'Este campo es obligatorio.',
    'email' => 'Escribe un correo válido.',
    'regex' => 'El formato de este campo no es válido.',
    'unique' => 'Este valor ya está en uso.',
    'numeric' => 'Debe ser un número.',
    'integer' => 'Debe ser un número entero.',
    'image' => 'Debe ser una imagen.',
    'confirmed' => 'La confirmación no coincide.',
    'same' => 'Los campos no coinciden.',
    'between' => ['numeric' => 'Debe estar entre :min y :max.'],
    'min' => ['numeric' => 'Debe ser al menos :min.', 'string' => 'Debe tener al menos :min caracteres.', 'array' => 'Agrega al menos :min.', 'file' => 'Debe pesar al menos :min KB.'],
    'max' => ['numeric' => 'No puede ser mayor que :max.', 'string' => 'No puede tener más de :max caracteres.', 'array' => 'No puede tener más de :max elementos.', 'file' => 'No puede pesar más de :max KB.'],
    'password' => [
        'letters' => 'La clave debe tener letras.',
        'mixed' => 'La clave debe tener mayúsculas y minúsculas.',
        'numbers' => 'La clave debe tener números.',
        'symbols' => 'La clave debe tener símbolos.',
        'uncompromised' => 'Esta clave apareció en una filtración. Elige otra.',
    ],
    'attributes' => [],
];
