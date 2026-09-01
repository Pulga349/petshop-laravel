<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Facade;
use Illuminate\Support\Facades\Session;

/**
 * Helper para notificaciones toastflash()
 * 
 * Usage en controladores:
 *   toast('success', 'Producto creado correctamente');
 *   toast('error', 'Error al guardar');
 *   toast('warning', 'Stock bajo');
 *   toast('info', 'Nuevo mensaje');
 * 
 * Con título:
 *   toast('success', 'Guardado', '¡Listo!');
 */
function toast(string $type, string $message, ?string $title = null, int $duration = 5000): void
{
    Session::flash('alert.toast', [
        'type' => $type,
        'message' => $message,
        'title' => $title,
        'duration' => $duration,
    ]);
}

/**
 * Shortcuts
 */
function toast_success(string $message, ?string $title = null): void
{
    toast('success', $message, $title);
}

function toast_error(string $message, ?string $title = null): void
{
    toast('error', $message, $title, 0);
}

function toast_warning(string $message, ?string $title = null): void
{
    toast('warning', $message, $title, 7000);
}

function toast_info(string $message, ?string $title = null): void
{
    toast('info', $message, $title);
}