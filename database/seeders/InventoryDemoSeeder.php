<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Product;
use App\Models\Warehouse;
use App\Models\Shelf;
use App\Models\Stock;
use App\Models\StockMovement;
use App\Models\User;

class InventoryDemoSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first();

        $cElk = Category::where('code', 'ELK')->first();
        $cMek = Category::where('code', 'MEK')->first();
        $cRob = Category::where('code', 'ROB')->first();
        $cKrt = Category::where('code', 'KRT')->first();

        $wElek = Warehouse::where('name', 'Ana Elektronik Deposu')->first();
        $wMek = Warehouse::where('name', 'Mekanik ve Atölye Deposu')->first();
        $wGenel = Warehouse::where('name', 'Genel Sarf Deposu')->first();

        $shelfA1 = Shelf::where('code', 'A-01')->first();
        $shelfA2 = Shelf::where('code', 'A-02')->first();
        $shelfB1 = Shelf::where('code', 'B-01')->first();
        $shelfC1 = Shelf::where('code', 'C-01')->first();

        $productsData = [
            [
                'product_code' => 'ELK-000001',
                'name' => 'Arduino UNO R3',
                'category_id' => $cElk->id,
                'description' => 'ATmega328P tabanlı geliştirme kartı',
                'unit' => 'Adet',
                'minimum_stock' => 20,
                'warehouse_id' => $wElek->id,
                'shelf_id' => $shelfA1->id,
                'stock' => 150,
            ],
            [
                'product_code' => 'ELK-000002',
                'name' => 'HC-SR04 Ultrasonik Mesafe Sensörü',
                'category_id' => $cElk->id,
                'description' => '2cm-400cm ölçüm aralığı',
                'unit' => 'Adet',
                'minimum_stock' => 30,
                'warehouse_id' => $wElek->id,
                'shelf_id' => $shelfA2->id,
                'stock' => 85,
            ],
            [
                'product_code' => 'ELK-000003',
                'name' => 'Raspberry Pi 4 Model B (4GB)',
                'category_id' => $cElk->id,
                'description' => 'Dört çekirdekli mini bilgisayar kartı',
                'unit' => 'Adet',
                'minimum_stock' => 10,
                'warehouse_id' => $wElek->id,
                'shelf_id' => $shelfA1->id,
                'stock' => 4, // Critical stock demonstration
            ],
            [
                'product_code' => 'ROB-000001',
                'name' => 'MG996R Servo Motor',
                'category_id' => $cRob->id,
                'description' => 'Yüksek torklu metal dişli servo',
                'unit' => 'Adet',
                'minimum_stock' => 25,
                'warehouse_id' => $wMek->id,
                'shelf_id' => $shelfB1->id,
                'stock' => 12, // Critical stock demonstration
            ],
            [
                'product_code' => 'MEK-000001',
                'name' => 'M3x10mm Civata Seti (100lü)',
                'category_id' => $cMek->id,
                'description' => 'Paslanmaz alyen başlı civata',
                'unit' => 'Paket',
                'minimum_stock' => 15,
                'warehouse_id' => $wMek->id,
                'shelf_id' => $shelfB1->id,
                'stock' => 40,
            ],
            [
                'product_code' => 'KRT-000001',
                'name' => 'A4 Fotokopi Kağıdı (80gr)',
                'category_id' => $cKrt->id,
                'description' => '500lü paket beyaz kağıt',
                'unit' => 'Koli',
                'minimum_stock' => 10,
                'warehouse_id' => $wGenel->id,
                'shelf_id' => $shelfC1->id,
                'stock' => 50,
            ],
        ];

        foreach ($productsData as $pData) {
            $product = Product::firstOrCreate(
                ['product_code' => $pData['product_code']],
                [
                    'name' => $pData['name'],
                    'category_id' => $pData['category_id'],
                    'description' => $pData['description'],
                    'unit' => $pData['unit'],
                    'minimum_stock' => $pData['minimum_stock'],
                ]
            );

            $stock = Stock::firstOrCreate(
                [
                    'product_id' => $product->id,
                    'warehouse_id' => $pData['warehouse_id'],
                    'shelf_id' => $pData['shelf_id'],
                ],
                [
                    'quantity' => $pData['stock'],
                ]
            );

            StockMovement::create([
                'product_id' => $product->id,
                'warehouse_id' => $pData['warehouse_id'],
                'shelf_id' => $pData['shelf_id'],
                'user_id' => $user->id ?? 1,
                'type' => 'in',
                'old_quantity' => 0,
                'new_quantity' => $pData['stock'],
                'change_amount' => $pData['stock'],
                'reason' => 'İlk Sayım / Depo Açılış Kaydı',
            ]);
        }
    }
}
