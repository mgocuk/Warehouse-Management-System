<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Elektronik', 'code' => 'ELK', 'description' => 'Sensörler, mikrokontrolcüler ve elektronik komponentler'],
            ['name' => 'Mekanik', 'code' => 'MEK', 'description' => 'Dişliler, motorlar, rulmanlar ve bağlantı elemanları'],
            ['name' => 'Robotik', 'code' => 'ROB', 'description' => 'Servo motorlar, kontrol kartları ve şasiler'],
            ['name' => 'Kırtasiye', 'code' => 'KRT', 'description' => 'Ofis, kağıt ve kırtasiye gereçleri'],
            ['name' => 'Kimyasal Malzemeler', 'code' => 'KIM', 'description' => 'Deney sıvıları, çözeltiler ve koruyucu sarf malzemeler'],
        ];

        foreach ($categories as $cat) {
            Category::firstOrCreate(['code' => $cat['code']], $cat);
        }
    }
}