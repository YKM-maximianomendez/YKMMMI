<?php

namespace App\Enums\Mantenimiento;

enum TipoEvidenciaFalla: string
{
    case FALLA = 'F';
    case REPARACION = 'R';
}
