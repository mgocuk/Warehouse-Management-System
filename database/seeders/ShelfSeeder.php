<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Shelf;
use App\Models\Warehouse;

class ShelfSeeder extends Seeder
{
    public function run(): void
    {
        $wElek = Warehouse::where('name', 'Ana Elektronik Deposu')->first();
        $wMek = Warehouse::where('name', 'Mekanik ve Atölye Deposu')->first();
        $wGenel = Warehouse::where('name', 'Genel Sarf Deposu')->first();

        if ($wElek) {
            Shelf::firstOrCreate(['code' => 'A-01'], ['warehouse_id' => $wElek->id, 'name' => 'Mikroişlemciler Raflığı', 'floor' => '1. Kat', 'row' => 'Sıra 1', 'description' => 'Arduino, Raspberry vb.']);
            Shelf::firstOrCreate(['code' => 'A-02'], ['warehouse_id' => $wElek->id, 'name' => 'Sensörler Raflığı', 'floor' => '1. Kat', 'row' => 'Sıra 2', 'description' => 'Mesafe, sıcaklık ve gaz sensörleri']);
        }

        if ($wMek) {
            Shelf::firstOrCreate(['code' => 'B-01'], ['warehouse_id' => $wMek->id, 'name' => 'Motorlar ve Dişliler', 'floor' => 'Zemin', 'row' => 'Sıra 1', 'description' => 'DC Motorlar ve Redüktörler']);
            Shelf::firstOrCreate(['code' => 'B-02'], ['warehouse_id' => $wMek->id, 'name' => 'Vidalar ve Bağlantı Elemanları', 'floor' => 'Zemin', 'row' => 'Sıra 2', 'description' => 'Somun, civata setleri']);
        }

        if ($wGenel) {
            Shelf::firstOrCreate(['code' => 'C-01'], ['warehouse_id' => $wGenel->id, 'name' => 'Kağıt ve Sarf Ürünleri', 'floor' => 'Bodrum', 'row' => 'Sıra 1', 'description' => 'A4 Kağıtlar ve Bantlar']);
        }
    }
}
