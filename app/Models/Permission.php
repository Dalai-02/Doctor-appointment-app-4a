<?php

namespace App\Models;

use Spatie\Permission\Models\Permission as SpatiePermission;

class Permission extends SpatiePermission
{
    /**
     * Los atributos que se pueden asignar en masa.
     */
    protected $fillable = ['name', 'guard_name'];

    /**
     * Valores por defecto para los atributos.
     */
    protected $attributes = [
        'guard_name' => 'web',
    ];
}
