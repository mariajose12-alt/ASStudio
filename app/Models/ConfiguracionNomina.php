<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConfiguracionNomina extends Model
{
    protected $table = 'configuracion_nomina';
    protected $fillable = ['sueldo_base_default', 'id'];

    public static function actual(): self
    {
        return self::firstOrCreate(['id' => 1], ['salario_base' => '0']);
    }
}
