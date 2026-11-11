<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        foreach ([
            'LAPTOP' => 'Laptop',
            'DESKTOP' => 'Desktop',
            'PRINTER' => 'Printer',
            'MOBILE' => 'Mobile phone',
            'OFFICE-EQUIPMENT' => 'Office equipment',
        ] as $code => $name) {
            Category::query()->firstOrCreate(
                ['code' => $code],
                ['name' => $name],
            );
        }
    }
}
