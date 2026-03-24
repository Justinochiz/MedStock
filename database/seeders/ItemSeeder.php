<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Generator as Faker;
use App\Models\Item;
use App\Models\Stock;

class ItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(Faker $faker): void
    {
        $items = [
            [
                'description' => 'Defibrillator (AED)',
                'category' => 'Cardiac Care',
                'brand' => 'Philips',
                'cost_price' => 15000.00,
                'sell_price' => 25000.00,
                'img_path' => 'images/Defibrillator (AED).jpg',
                'quantity' => 89
            ],
            [
                'description' => 'Defibrillator (AED) - Model 2',
                'category' => 'Cardiac Care',
                'brand' => 'Zoll',
                'cost_price' => 16000.00,
                'sell_price' => 26000.00,
                'img_path' => 'images/Defibrillator (AED)1.jpg',
                'quantity' => 56
            ],
            [
                'description' => 'EKG Machine',
                'category' => 'Diagnostic Equipment',
                'brand' => 'GE Healthcare',
                'cost_price' => 45000.00,
                'sell_price' => 65000.00,
                'img_path' => 'images/EKG Machine.jpg',
                'quantity' => 37
            ],
            [
                'description' => 'First Aid Kit 2',
                'category' => 'First Aid',
                'brand' => 'Red Cross',
                'cost_price' => 1500.00,
                'sell_price' => 2500.00,
                'img_path' => 'images/firstaid2.jpg',
                'quantity' => 90
            ],
            [
                'description' => 'Hospital Bed (Electric)',
                'category' => 'Furniture',
                'brand' => 'Stryker',
                'cost_price' => 35000.00,
                'sell_price' => 55000.00,
                'img_path' => 'images/Hospital Bed (Electric).jpg',
                'quantity' => 52
            ],
            [
                'description' => 'Hospital Bed (Electric) - Model 1',
                'category' => 'Furniture',
                'brand' => 'Hill-Rom',
                'cost_price' => 38000.00,
                'sell_price' => 58000.00,
                'img_path' => 'images/Hospital Bed (Electric)1.jpg',
                'quantity' => 29
            ],
            [
                'description' => 'Hospital Bed (Electric) - Model 2',
                'category' => 'Furniture',
                'brand' => 'Völker',
                'cost_price' => 40000.00,
                'sell_price' => 60000.00,
                'img_path' => 'images/Hospital Bed (Electric)2.jpg',
                'quantity' => 100
            ],
            [
                'description' => 'Infusion Pump',
                'category' => 'Infusion Systems',
                'brand' => 'Baxter',
                'cost_price' => 8000.00,
                'sell_price' => 12000.00,
                'img_path' => 'images/Infusion Pump.jpg',
                'quantity' => 84
            ],
            [
                'description' => 'Infusion Pump - Model 1',
                'category' => 'Infusion Systems',
                'brand' => 'Medtronic',
                'cost_price' => 8500.00,
                'sell_price' => 12500.00,
                'img_path' => 'images/Infusion Pump1.jpg',
                'quantity' => 72
            ],
            [
                'description' => 'Ophthalmoscope',
                'category' => 'Diagnostic Equipment',
                'brand' => 'Welch Allyn',
                'cost_price' => 2500.00,
                'sell_price' => 4500.00,
                'img_path' => 'images/Ophthalmoscope.jpeg',
                'quantity' => 67
            ],
            [
                'description' => 'Patient Monitor',
                'category' => 'Monitoring Equipment',
                'brand' => 'Philips',
                'cost_price' => 28000.00,
                'sell_price' => 42000.00,
                'img_path' => 'images/Patient Monitor.jpg',
                'quantity' => 44
            ],
            [
                'description' => 'Pulse Oximeter',
                'category' => 'Monitoring Equipment',
                'brand' => 'Nonin',
                'cost_price' => 1200.00,
                'sell_price' => 2500.00,
                'img_path' => 'images/Pulse Oximeter.jpg',
                'quantity' => 158
            ],
            [
                'description' => 'Surgical Scalpel',
                'category' => 'Surgical Instruments',
                'brand' => 'Swann Morton',
                'cost_price' => 50.00,
                'sell_price' => 150.00,
                'img_path' => 'images/scalpel.jpg',
                'quantity' => 56
            ],
            [
                'description' => 'Sphygmomanometer',
                'category' => 'Blood Pressure',
                'brand' => 'Omron',
                'cost_price' => 800.00,
                'sell_price' => 1800.00,
                'img_path' => 'images/Sphygmomanometer.jpg',
                'quantity' => 121
            ],
            [
                'description' => 'Surgical Instruments (Set)',
                'category' => 'Surgical Instruments',
                'brand' => 'Aesculap',
                'cost_price' => 5000.00,
                'sell_price' => 9000.00,
                'img_path' => 'images/Surgical Instruments (Set).jpg',
                'quantity' => 43
            ],
            [
                'description' => 'Surgical Instruments (Set) - Model 1',
                'category' => 'Surgical Instruments',
                'brand' => 'Medicon',
                'cost_price' => 5500.00,
                'sell_price' => 9500.00,
                'img_path' => 'images/Surgical Instruments (Set)1.jpg',
                'quantity' => 38
            ],
            [
                'description' => 'Thermometer (Digital)',
                'category' => 'Diagnostic Equipment',
                'brand' => 'Braun',
                'cost_price' => 300.00,
                'sell_price' => 800.00,
                'img_path' => 'images/Thermometer.jpg',
                'quantity' => 85
            ],
            [
                'description' => 'Ventilator (Transport)',
                'category' => 'Respiratory Care',
                'brand' => 'Ventilator Corp',
                'cost_price' => 25000.00,
                'sell_price' => 38000.00,
                'img_path' => 'images/Ventilator (Transport).jpg',
                'quantity' => 67
            ],
            [
                'description' => 'Ventilator (Transport) - Model 1',
                'category' => 'Respiratory Care',
                'brand' => 'Hamilton',
                'cost_price' => 26000.00,
                'sell_price' => 39000.00,
                'img_path' => 'images/Ventilator (Transport)1.jpg',
                'quantity' => 69
            ],
            [
                'description' => 'Ventilator (Transport) - Model 2',
                'category' => 'Respiratory Care',
                'brand' => 'Vyaire',
                'cost_price' => 27000.00,
                'sell_price' => 40000.00,
                'img_path' => 'images/Ventilator (Transport)2.jpg',
                'quantity' => 49
            ],
            [
                'description' => 'Wheelchair - Standard',
                'category' => 'Mobility',
                'brand' => 'Invacare',
                'cost_price' => 8000.00,
                'sell_price' => 12000.00,
                'img_path' => 'images/wheelchair1.jpg',
                'quantity' => 63
            ],
            [
                'description' => 'Wheelchair - Manual',
                'category' => 'Mobility',
                'brand' => 'Quickie',
                'cost_price' => 8500.00,
                'sell_price' => 12500.00,
                'img_path' => 'images/wheelchair2.jpg',
                'quantity' => 58
            ],
            [
                'description' => 'Wheelchair - Lightweight',
                'category' => 'Mobility',
                'brand' => 'Drive',
                'cost_price' => 7500.00,
                'sell_price' => 11500.00,
                'img_path' => 'images/wheelchair3.jpg',
                'quantity' => 62
            ],
        ];

        foreach ($items as $itemData) {
            $item = new Item();
            $item->description = $itemData['description'];
            $item->category = $itemData['category'];
            $item->brand = $itemData['brand'];
            $item->cost_price = $itemData['cost_price'];
            $item->sell_price = $itemData['sell_price'];
            $item->img_path = $itemData['img_path'];
            $item->save();

            $stock = new Stock();
            $stock->item_id = (int) $item->getKey();
            $stock->quantity = $itemData['quantity'];
            $stock->save();
        }
    }
}
