<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Warehouse;

class WarehouseSeeder extends Seeder
{
    public function run(): void
    {
        $warehouses = [
            ['name' => 'Ana Elektronik Deposu', 'location' => 'Bina A - Kat 1', 'description' => 'Elektronik ve robotik donanımların saklandığı depo'],
            ['name' => 'Mekanik ve Atölye Deposu', 'location' => 'Bina B - Zemin Kat', 'description' => 'Ağır mekanik parçalar ve el aletleri deposu'],
            ['name' => 'Genel Sarf Deposu', 'location' => 'Bina A - Bodrum Kat', 'description' => 'Kırtasiye, ambalaj ve genel malzeme deposu'],
        ];

        foreach ($warehouses as $w) {
            Warehouse::firstOrCreate(['name' => $w['name']], $w);
        }
    }
}
