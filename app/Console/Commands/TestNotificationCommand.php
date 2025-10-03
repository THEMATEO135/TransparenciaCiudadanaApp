<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Notification;

class TestNotificationCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:notification {userId?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envía una notificación de prueba a un usuario admin';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $userId = $this->argument('userId');

        if (!$userId) {
            // Buscar el primer admin
            $user = User::where('role', 'admin')->first();

            if (!$user) {
                $this->error('No se encontró ningún usuario admin en la base de datos.');
                $this->info('Crea un usuario admin primero.');
                return 1;
            }
        } else {
            $user = User::find($userId);

            if (!$user) {
                $this->error("No se encontró el usuario con ID: {$userId}");
                return 1;
            }
        }

        $this->info("Enviando notificación de prueba a: {$user->email} (ID: {$user->id})");

        // Crear notificación de prueba
        $notification = Notification::createFor(
            $user->id,
            'test',
            '🔔 Notificación de Prueba',
            'Esta es una notificación de prueba del sistema de tiempo real. Si ves esto, ¡las notificaciones push están funcionando!',
            route('admin.dashboard')
        );

        $this->info('✅ Notificación creada exitosamente!');
        $this->info("ID de notificación: {$notification->id}");
        $this->newLine();
        $this->info('Verifica en el navegador:');
        $this->info('1. Abre el dashboard admin');
        $this->info('2. Abre la consola del navegador (F12)');
        $this->info('3. Deberías ver el evento de notificación');

        return 0;
    }
}
