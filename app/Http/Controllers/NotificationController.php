<?php

namespace App\Http\Controllers;

use App\Notification;
use Illuminate\Http\Request;
use Auth;

class NotificationController extends Controller
{
    public function get() {
        //return Notification::all();
        // Notificaciones no leidas
        $unreadNotifications = Auth::user()->unreadNotifications;
        $fechaActual = date('Y-m-d');

        foreach ($unreadNotifications as $notification) {
            // Si notificacion no leida no coincide con el dia actual, marca como leida
            if ($fechaActual != $notification->created_at->toDateString()) {
                $notification->markAsRead();
            }
        }
        return Auth::user()->unreadNotifications; // Muestra notificaciones del usuario logueado
    }
}
