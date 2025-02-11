<?php

namespace App\Enums;

enum Roles: string
{
    case TECNICO_REPARADOR = "Tecnico Reparador";
    case LIDER_PRENSAS = "Lider Prensas";
    case LIDER_TOOLROOM = "Lider ToolRoom";
    case ADMINISTRADOR_PRENSAS = "Administrador Prensas";
    case ADMINISTRADOR_TOOLROOM = "Administrador ToolRoom";
    case ADMINISTRADOR_IT = "Administrador IT";
}
