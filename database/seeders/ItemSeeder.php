<?php

namespace Database\Seeders;

use App\Models\Item;
use Illuminate\Database\Seeder;

class ItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $items = [
            [
                'name' => 'Canon EOS 5D Mark IV',
                'description' => 'Kamera DSLR profesional dengan sensor full-frame 30.4 megapixel. Dilengkapi dengan fitur video 4K dan sistem autofocus 61 titik.',
                'daily_price' => 350000,
            ],
            [
                'name' => 'Canon EF 24-70mm f/2.8L II',
                'description' => 'Lensa zoom profesional dengan aperture maksimal f/2.8. Ideal untuk portrait, landscape, dan event photography.',
                'daily_price' => 150000,
            ],
            [
                'name' => 'Sony A7 III',
                'description' => 'Kamera mirrorless full-frame dengan 24.2MP sensor. Mendukung 4K video dan memiliki stabilisasi 5-axis.',
                'daily_price' => 400000,
            ],
            [
                'name' => 'Tripod Manfrotto 055',
                'description' => 'Tripod aluminium heavy duty dengan tinggi maksimal 183cm. Cocok untuk studio dan outdoor photography.',
                'daily_price' => 50000,
            ],
            [
                'name' => 'LED Panel Godox SL-60W',
                'description' => 'Lampu LED continuous 60W dengan color temperature 5600K. Silent fan mode untuk video shooting.',
                'daily_price' => 100000,
            ],
            [
                'name' => 'Canon EF 70-200mm f/2.8L IS III',
                'description' => 'Lensa telephoto profesional dengan stabilisasi gambar. Ideal untuk sport dan wildlife photography.',
                'daily_price' => 200000,
            ],
            [
                'name' => 'DJI Ronin-S',
                'description' => 'Gimbal stabilizer 3-axis untuk kamera mirrorless dan DSLR. Max payload 3.6kg.',
                'daily_price' => 175000,
            ],
            [
                'name' => 'Rode VideoMic Pro+',
                'description' => 'Mikrofon shotgun on-camera dengan baterai rechargeable. HF dan LF filter.',
                'daily_price' => 75000,
            ],
        ];

        foreach ($items as $itemData) {
            Item::create($itemData);
        }
    }
}
