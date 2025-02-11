<?php

namespace App\Enums;

enum TiposArchivo: string
{
    case JPEG = 'image/jpeg';
    case PNG = 'image/png';
    case GIF = 'image/gif';
    case BMP = 'image/bmp';
    case SVG = 'image/svg+xml';
    case DOC = 'application/msword';
    case DOCX = 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';
    case XLS = 'application/vnd.ms-excel';
    case XLSX = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
    case PPT = 'application/vnd.ms-powerpoint';
    case PPTX = 'application/vnd.openxmlformats-officedocument.presentationml.presentation';
    case PDF = 'application/pdf';
    case TXT = 'text/plain';
    case JSON = 'application/json';
    case XML = 'application/xml';
}
