<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Crea (o actualiza) el usuario administrador "admin".
 * La contraseña no se guarda en el código: se lee de la variable ADMIN_PASSWORD.
 *
 *   ADMIN_PASSWORD='...' php artisan db:seed --class=AdminSeeder
 */
class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $password = env('ADMIN_PASSWORD');

        if (! $password) {
            $this->command->error('Falta la variable ADMIN_PASSWORD.');

            return;
        }

        $admin = User::firstOrNew(['username' => 'admin']);
        $admin->name = 'Administrador';
        $admin->email = env('ADMIN_EMAIL', 'admin@yoguifit.com');
        $admin->password = $password; // el modelo la cifra (cast "hashed")
        $admin->is_admin = true;
        $admin->save();

        $this->command->info('Usuario admin listo.');
    }
}
