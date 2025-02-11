<?php

namespace App\Services;

use Exception;

class TurnoService
{
    public function consultar_turno(string $fecha = null): int
    {
        $timestamp = strtotime($fecha ?? now()->toTimeString());

        $periodo = [
            'd_inicia_turno'    => strtotime('08:00:00'),
            'd_fin_turno'       => strtotime('17:36:59'),
            'n_inicia_turno'    => strtotime('17:37:00'),
            'n_fin_turno'       => strtotime('07:59:59 +1 day')
        ];

        if ($timestamp === false) {
            throw new Exception("Fecha no válida.");
        }

        if ($timestamp >= $periodo['d_inicia_turno'] && $timestamp <= $periodo['d_fin_turno']) {
            return 1;
        }

        if ($timestamp >= $periodo['n_inicia_turno'] || $timestamp <= $periodo['n_fin_turno']) {
            return 2;
        }

        throw new Exception("Turno no encontrado.");
    }
}
