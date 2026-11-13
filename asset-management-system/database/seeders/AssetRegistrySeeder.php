<?php

namespace Database\Seeders;

use App\Models\Asset;
use App\Models\Category;
use App\Models\Location;
use Illuminate\Database\Seeder;

class AssetRegistrySeeder extends Seeder
{
    public function run(): void
    {
        $categoryNames = [
            'LAPTOP' => 'Laptop',
            'MOBILE-PHONE' => 'Mobile Phone',
            'TABLET' => 'Tablet',
        ];

        foreach ($categoryNames as $code => $name) {
            Category::query()->firstOrCreate(
                ['code' => $code],
                ['name' => $name, 'is_active' => true],
            );
        }

        $categories = Category::query()
            ->whereIn('code', array_keys($categoryNames))
            ->get()
            ->keyBy('code');

        Location::query()->firstOrCreate(
            ['name' => 'Demo Main Office'],
            ['is_active' => true],
        );

        $assets = [
            [
                'asset_code' => 'DEMO-AST-1001', 'name' => 'Demo Laptop 01',
                'category_code' => 'LAPTOP', 'serial_number' => 'DEMO-SN-0001',
                'purchase_date' => '2024-01-15', 'purchase_cost' => '1250.00',
                'warranty_expiry_date' => '2026-01-15', 'condition' => 'good',
                'status' => 'available',
            ],
            [
                'asset_code' => 'DEMO-AST-1002', 'name' => 'Demo Laptop 02',
                'category_code' => 'LAPTOP', 'serial_number' => 'DEMO-SN-0002',
                'purchase_date' => null, 'purchase_cost' => null, 'warranty_expiry_date' => null,
                'condition' => 'good', 'status' => 'available',
            ],
            [
                'asset_code' => 'DEMO-AST-1003', 'name' => 'Demo Laptop 03',
                'category_code' => 'LAPTOP', 'serial_number' => 'DEMO-SN-0003',
                'purchase_date' => null, 'purchase_cost' => null, 'warranty_expiry_date' => null,
                'condition' => 'good', 'status' => 'damaged',
            ],
            [
                'asset_code' => 'DEMO-AST-1004', 'name' => 'Demo Tablet 01',
                'category_code' => 'TABLET', 'serial_number' => 'DEMO-SN-0004',
                'purchase_date' => null, 'purchase_cost' => null, 'warranty_expiry_date' => null,
                'condition' => 'good', 'status' => 'available',
            ],
            [
                'asset_code' => 'DEMO-AST-1005', 'name' => 'Demo Mobile Phone 01',
                'category_code' => 'MOBILE-PHONE', 'serial_number' => 'DEMO-SN-0005',
                'purchase_date' => null, 'purchase_cost' => null, 'warranty_expiry_date' => null,
                'condition' => 'good', 'status' => 'disposed',
            ],
            [
                'asset_code' => 'DEMO-AST-1006', 'name' => 'Demo Mobile Phone 02',
                'category_code' => 'MOBILE-PHONE', 'serial_number' => 'DEMO-SN-0006',
                'purchase_date' => '2024-02-04', 'purchase_cost' => '450.00',
                'warranty_expiry_date' => '2026-02-04', 'condition' => 'poor',
                'status' => 'available',
            ],
            [
                'asset_code' => 'DEMO-AST-1007', 'name' => 'Demo Mobile Phone 03',
                'category_code' => 'MOBILE-PHONE', 'serial_number' => 'DEMO-SN-0007',
                'purchase_date' => '2024-03-04', 'purchase_cost' => '520.00',
                'warranty_expiry_date' => '2026-03-04', 'condition' => 'fair',
                'status' => 'available',
            ],
            [
                'asset_code' => 'DEMO-AST-1008', 'name' => 'Demo Laptop 04',
                'category_code' => 'LAPTOP', 'serial_number' => 'DEMO-SN-0008',
                'purchase_date' => '2024-04-05', 'purchase_cost' => '1120.00',
                'warranty_expiry_date' => '2026-04-05', 'condition' => 'good',
                'status' => 'available',
            ],
            [
                'asset_code' => 'DEMO-AST-1009', 'name' => 'Demo Tablet 02',
                'category_code' => 'TABLET', 'serial_number' => 'DEMO-SN-0009',
                'purchase_date' => '2024-05-06', 'purchase_cost' => '325.00',
                'warranty_expiry_date' => '2026-05-06', 'condition' => 'good',
                'status' => 'available',
            ],
            [
                'asset_code' => 'DEMO-AST-1010', 'name' => 'Demo Tablet 03',
                'category_code' => 'TABLET', 'serial_number' => 'DEMO-SN-0010',
                'purchase_date' => '2024-06-07', 'purchase_cost' => '375.00',
                'warranty_expiry_date' => '2026-06-07', 'condition' => 'good',
                'status' => 'available',
            ],
            [
                'asset_code' => 'DEMO-AST-1011', 'name' => 'Demo Tablet 04',
                'category_code' => 'TABLET', 'serial_number' => 'DEMO-SN-0011',
                'purchase_date' => '2024-07-08', 'purchase_cost' => '390.00',
                'warranty_expiry_date' => '2026-07-08', 'condition' => 'good',
                'status' => 'available',
            ],
            [
                'asset_code' => 'DEMO-AST-1012', 'name' => 'Demo Laptop 05',
                'category_code' => 'LAPTOP', 'serial_number' => 'DEMO-SN-0012',
                'purchase_date' => '2024-08-09', 'purchase_cost' => '1350.00',
                'warranty_expiry_date' => '2026-08-09', 'condition' => 'good',
                'status' => 'available',
            ],
            [
                'asset_code' => 'DEMO-AST-1013', 'name' => 'Demo Laptop 06',
                'category_code' => 'LAPTOP', 'serial_number' => 'DEMO-SN-0013',
                'purchase_date' => '2024-09-10', 'purchase_cost' => '1425.00',
                'warranty_expiry_date' => '2026-09-10', 'condition' => 'good',
                'status' => 'available',
            ],
            [
                'asset_code' => 'DEMO-AST-1014', 'name' => 'Demo Laptop 07',
                'category_code' => 'LAPTOP', 'serial_number' => 'DEMO-SN-0014',
                'purchase_date' => '2024-10-11', 'purchase_cost' => '980.00',
                'warranty_expiry_date' => '2026-10-11', 'condition' => 'good',
                'status' => 'available',
            ],
            [
                'asset_code' => 'DEMO-AST-1015', 'name' => 'Demo Laptop 08',
                'category_code' => 'LAPTOP', 'serial_number' => 'DEMO-SN-0015',
                'purchase_date' => '2024-11-12', 'purchase_cost' => '1045.00',
                'warranty_expiry_date' => '2026-11-12', 'condition' => 'good',
                'status' => 'available',
            ],
        ];

        foreach ($assets as $assetData) {
            Asset::query()->firstOrCreate(
                ['asset_code' => $assetData['asset_code']],
                [
                    'category_id' => $categories[$assetData['category_code']]->id,
                    'name' => $assetData['name'],
                    'serial_number' => $assetData['serial_number'],
                    'purchase_date' => $assetData['purchase_date'],
                    'purchase_cost' => $assetData['purchase_cost'],
                    'warranty_expiry_date' => $assetData['warranty_expiry_date'],
                    'condition' => $assetData['condition'],
                    'status' => $assetData['status'],
                ],
            );
        }
    }
}
