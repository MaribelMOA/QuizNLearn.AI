<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\XpTransaction;
use App\Models\User;
use App\Models\XpPrice;

class XpTransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $xpPrices = XpPrice::all();

        // Verifica si existen usuarios y precios de XP
        if ($users->isEmpty() || $xpPrices->isEmpty()) {
            $this->command->warn('No hay usuarios o precios de XP disponibles para crear transacciones.');
            return;
        }

        // Para cada usuario, crea entre 1 y 3 transacciones de XP
        foreach ($users as $user) {
            for ($i = 0; $i < rand(1, 3); $i++) {
                $xpPrice = $xpPrices->random(); // Selecciona un precio de XP aleatorio

                XpTransaction::create([
                    'user_id' => $user->id,
                    'xp_price_id' => $xpPrice->id,
                    'created_at' => now(),  // Puedes cambiar esto si necesitas fechas personalizadas
                ]);
            }
        }
    }
}
