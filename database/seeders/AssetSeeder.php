<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Asset;
use App\Models\Category;
use App\Models\Vendor;
use App\Models\Location;
use App\Models\User;
use Carbon\Carbon;

class AssetSeeder extends Seeder
{
    public function run()
    {
        // Get existing data
        $categories = Category::all();
        $vendors = Vendor::all();
        $locations = Location::all();
        $users = User::all();
        
        $this->command->info('Found ' . $categories->count() . ' categories, ' . $vendors->count() . ' vendors, ' . $locations->count() . ' locations, ' . $users->count() . ' users');
        
        if ($categories->isEmpty() || $vendors->isEmpty() || $locations->isEmpty() || $users->isEmpty()) {
            $this->command->error('Required data not found. Please run CategorySeeder, VendorSeeder, LaboratorySeeder, and CreateDefaultUserSeeder first.');
            return;
        }

        $assets = [
            // Computer Systems
            [
                'name' => 'Dell OptiPlex 7090 Desktop',
                'category_id' => $categories->where('name', 'Hardware')->first()->id,
                'location_id' => $locations->random()->id,
                'status' => 'IN USE',
                'model' => 'OptiPlex 7090',
                'specification' => 'Intel Core i7-11700, 16GB RAM, 512GB SSD, Windows 11 Pro',
                'vendor_id' => $vendors->where('name', 'Dell Technologies')->first()->id,
                'purchase_date' => Carbon::now()->subMonths(rand(6, 24)),
                'warranty_period' => Carbon::now()->addYears(3),
                'purchase_price' => 85000.00,
                'created_by' => $users->random()->id,
            ],
            [
                'name' => 'HP EliteBook 850 G8 Laptop',
                'category_id' => $categories->where('name', 'Hardware')->first()->id,
                'location_id' => $locations->random()->id,
                'status' => 'IN USE',
                'model' => 'EliteBook 850 G8',
                'specification' => 'Intel Core i5-1135G7, 8GB RAM, 256GB SSD, Windows 11 Pro',
                'vendor_id' => $vendors->where('name', 'HP Inc.')->first()->id,
                'purchase_date' => Carbon::now()->subMonths(rand(3, 18)),
                'warranty_period' => Carbon::now()->addYears(3),
                'purchase_price' => 65000.00,
                'created_by' => $users->random()->id,
            ],
            [
                'name' => 'Lenovo ThinkPad X1 Carbon',
                'category_id' => $categories->where('name', 'Hardware')->first()->id,
                'location_id' => $locations->random()->id,
                'status' => 'UNDER REPAIR',
                'model' => 'ThinkPad X1 Carbon Gen 9',
                'specification' => 'Intel Core i7-1165G7, 16GB RAM, 512GB SSD, Windows 11 Pro',
                'vendor_id' => $vendors->where('name', 'Lenovo Group Limited')->first()->id,
                'purchase_date' => Carbon::now()->subMonths(rand(8, 20)),
                'warranty_period' => Carbon::now()->addYears(3),
                'purchase_price' => 95000.00,
                'created_by' => $users->random()->id,
            ],
            [
                'name' => 'ASUS VivoBook S15',
                'category_id' => $categories->where('name', 'Hardware')->first()->id,
                'location_id' => $locations->random()->id,
                'status' => 'IN USE',
                'model' => 'S533EA',
                'specification' => 'Intel Core i5-1135G7, 8GB RAM, 512GB SSD, Windows 11 Home',
                'vendor_id' => $vendors->where('name', 'ASUS')->first()->id,
                'purchase_date' => Carbon::now()->subMonths(rand(4, 15)),
                'warranty_period' => Carbon::now()->addYears(2),
                'purchase_price' => 45000.00,
                'created_by' => $users->random()->id,
            ],
            [
                'name' => 'MacBook Pro 13-inch',
                'category_id' => $categories->where('name', 'Hardware')->first()->id,
                'location_id' => $locations->random()->id,
                'status' => 'IN USE',
                'model' => 'MacBook Pro 13-inch M1',
                'specification' => 'Apple M1 chip, 8GB RAM, 256GB SSD, macOS Monterey',
                'vendor_id' => $vendors->where('name', 'Apple Inc.')->first()->id,
                'purchase_date' => Carbon::now()->subMonths(rand(6, 18)),
                'warranty_period' => Carbon::now()->addYears(1),
                'purchase_price' => 75000.00,
                'created_by' => $users->random()->id,
            ],

            // Network Equipment
            [
                'name' => 'Cisco Catalyst 2960 Switch',
                'category_id' => $categories->where('name', 'Network')->first()->id,
                'location_id' => $locations->random()->id,
                'status' => 'IN USE',
                'model' => 'WS-C2960-24TC-L',
                'specification' => '24-port 10/100 Ethernet switch, 2 Gigabit uplinks',
                'vendor_id' => $vendors->where('name', 'Cisco Systems')->first()->id ?? $vendors->random()->id,
                'purchase_date' => Carbon::now()->subMonths(rand(12, 36)),
                'warranty_period' => Carbon::now()->addYears(1),
                'purchase_price' => 25000.00,
                'created_by' => $users->random()->id,
            ],
            [
                'name' => 'TP-Link Archer C7 Router',
                'category_id' => $categories->where('name', 'Network')->first()->id,
                'location_id' => $locations->random()->id,
                'status' => 'IN USE',
                'model' => 'AC1750',
                'specification' => 'Dual-band 802.11ac, 1.75 Gbps, 4 Gigabit LAN ports',
                'vendor_id' => $vendors->where('name', 'TP-Link')->first()->id ?? $vendors->random()->id,
                'purchase_date' => Carbon::now()->subMonths(rand(8, 24)),
                'warranty_period' => Carbon::now()->addYears(2),
                'purchase_price' => 8000.00,
                'created_by' => $users->random()->id,
            ],

            // Peripherals
            [
                'name' => 'Canon PIXMA G3110 Printer',
                'category_id' => $categories->where('name', 'Peripherals')->first()->id,
                'location_id' => $locations->random()->id,
                'status' => 'IN USE',
                'model' => 'PIXMA G3110',
                'specification' => 'All-in-one inkjet printer, wireless printing, refillable ink tanks',
                'vendor_id' => $vendors->where('name', 'Canon Inc.')->first()->id,
                'purchase_date' => Carbon::now()->subMonths(rand(6, 18)),
                'warranty_period' => Carbon::now()->addYears(1),
                'purchase_price' => 12000.00,
                'created_by' => $users->random()->id,
            ],
            [
                'name' => 'Epson WorkForce Pro WF-3720',
                'category_id' => $categories->where('name', 'Peripherals')->first()->id,
                'location_id' => $locations->random()->id,
                'status' => 'UNDER REPAIR',
                'model' => 'WF-3720',
                'specification' => 'All-in-one wireless printer, duplex printing, mobile printing',
                'vendor_id' => $vendors->where('name', 'Epson')->first()->id,
                'purchase_date' => Carbon::now()->subMonths(rand(10, 30)),
                'warranty_period' => Carbon::now()->subMonths(6),
                'purchase_price' => 15000.00,
                'created_by' => $users->random()->id,
            ],
            [
                'name' => 'Logitech C920 HD Pro Webcam',
                'category_id' => $categories->where('name', 'Peripherals')->first()->id,
                'location_id' => $locations->random()->id,
                'status' => 'IN USE',
                'model' => 'C920',
                'specification' => '1080p HD video calling, stereo audio, auto light correction',
                'vendor_id' => $vendors->where('name', 'Logitech')->first()->id ?? $vendors->random()->id,
                'purchase_date' => Carbon::now()->subMonths(rand(3, 12)),
                'warranty_period' => Carbon::now()->addYears(2),
                'purchase_price' => 5000.00,
                'created_by' => $users->random()->id,
            ],

            // Software
            [
                'name' => 'Microsoft Office 365 Business',
                'category_id' => $categories->where('name', 'Software')->first()->id,
                'location_id' => $locations->random()->id,
                'status' => 'IN USE',
                'model' => 'Office 365 Business Premium',
                'specification' => 'Word, Excel, PowerPoint, Outlook, Teams, OneDrive, SharePoint',
                'vendor_id' => $vendors->where('name', 'Microsoft Corporation')->first()->id,
                'purchase_date' => Carbon::now()->subMonths(rand(1, 6)),
                'warranty_period' => Carbon::now()->addYears(1),
                'purchase_price' => 15000.00,
                'created_by' => $users->random()->id,
            ],
            [
                'name' => 'Adobe Creative Cloud',
                'category_id' => $categories->where('name', 'Software')->first()->id,
                'location_id' => $locations->random()->id,
                'status' => 'IN USE',
                'model' => 'Creative Cloud All Apps',
                'specification' => 'Photoshop, Illustrator, InDesign, Premiere Pro, After Effects',
                'vendor_id' => $vendors->where('name', 'Adobe Inc.')->first()->id ?? $vendors->random()->id,
                'purchase_date' => Carbon::now()->subMonths(rand(2, 8)),
                'warranty_period' => Carbon::now()->addYears(1),
                'purchase_price' => 25000.00,
                'created_by' => $users->random()->id,
            ],

            // Additional Hardware
            [
                'name' => 'Samsung 24-inch Monitor',
                'category_id' => $categories->where('name', 'Peripherals')->first()->id,
                'location_id' => $locations->random()->id,
                'status' => 'IN USE',
                'model' => 'S24F350FH',
                'specification' => '24-inch Full HD LED monitor, 1920x1080, HDMI, VGA',
                'vendor_id' => $vendors->where('name', 'Samsung Electronics')->first()->id,
                'purchase_date' => Carbon::now()->subMonths(rand(6, 24)),
                'warranty_period' => Carbon::now()->addYears(1),
                'purchase_price' => 12000.00,
                'created_by' => $users->random()->id,
            ],
            [
                'name' => 'Dell OptiPlex 3080 Desktop',
                'category_id' => $categories->where('name', 'Hardware')->first()->id,
                'location_id' => $locations->random()->id,
                'status' => 'LOST',
                'model' => 'OptiPlex 3080',
                'specification' => 'Intel Core i3-10100, 8GB RAM, 256GB SSD, Windows 10 Pro',
                'vendor_id' => $vendors->where('name', 'Dell Technologies')->first()->id,
                'purchase_date' => Carbon::now()->subMonths(rand(12, 30)),
                'warranty_period' => Carbon::now()->subMonths(6),
                'purchase_price' => 55000.00,
                'lost_date' => Carbon::now()->subDays(rand(1, 30)),
                'lost_reason' => 'Missing from assigned location during inventory check',
                'created_by' => $users->random()->id,
            ],
            [
                'name' => 'HP LaserJet Pro M404n',
                'category_id' => $categories->where('name', 'Peripherals')->first()->id,
                'location_id' => $locations->random()->id,
                'status' => 'DISPOSED',
                'model' => 'LaserJet Pro M404n',
                'specification' => 'Monochrome laser printer, 38 ppm, 250-sheet input tray',
                'vendor_id' => $vendors->where('name', 'HP Inc.')->first()->id,
                'purchase_date' => Carbon::now()->subYears(rand(4, 6)),
                'warranty_period' => Carbon::now()->subYears(2),
                'purchase_price' => 18000.00,
                'disposal_date' => Carbon::now()->subDays(rand(1, 60)),
                'disposal_reason' => 'End of useful life, frequent paper jams and print quality issues',
                'created_by' => $users->random()->id,
            ],
            [
                'name' => 'Acer Aspire 5 Laptop',
                'category_id' => $categories->where('name', 'Hardware')->first()->id,
                'location_id' => $locations->random()->id,
                'status' => 'PULLED OUT',
                'model' => 'A515-56',
                'specification' => 'Intel Core i5-1135G7, 8GB RAM, 512GB SSD, Windows 11 Home',
                'vendor_id' => $vendors->where('name', 'Acer Inc.')->first()->id,
                'purchase_date' => Carbon::now()->subMonths(rand(8, 20)),
                'warranty_period' => Carbon::now()->addMonths(6),
                'purchase_price' => 48000.00,
                'created_by' => $users->random()->id,
            ],
            [
                'name' => 'Lenovo ThinkCentre M720',
                'category_id' => $categories->where('name', 'Hardware')->first()->id,
                'location_id' => $locations->random()->id,
                'status' => 'IN USE',
                'model' => 'ThinkCentre M720',
                'specification' => 'Intel Core i5-8400, 8GB RAM, 1TB HDD, Windows 10 Pro',
                'vendor_id' => $vendors->where('name', 'Lenovo Group Limited')->first()->id,
                'purchase_date' => Carbon::now()->subMonths(rand(18, 36)),
                'warranty_period' => Carbon::now()->subMonths(6),
                'purchase_price' => 42000.00,
                'created_by' => $users->random()->id,
            ],
            [
                'name' => 'ASUS ROG Strix G15 Gaming Laptop',
                'category_id' => $categories->where('name', 'Hardware')->first()->id,
                'location_id' => $locations->random()->id,
                'status' => 'IN USE',
                'model' => 'G513QE',
                'specification' => 'AMD Ryzen 7 5800H, 16GB RAM, 512GB SSD, RTX 3050 Ti, Windows 11',
                'vendor_id' => $vendors->where('name', 'ASUS')->first()->id,
                'purchase_date' => Carbon::now()->subMonths(rand(6, 15)),
                'warranty_period' => Carbon::now()->addYears(2),
                'purchase_price' => 85000.00,
                'created_by' => $users->random()->id,
            ],
            [
                'name' => 'Dell UltraSharp U2720Q Monitor',
                'category_id' => $categories->where('name', 'Peripherals')->first()->id,
                'location_id' => $locations->random()->id,
                'status' => 'IN USE',
                'model' => 'U2720Q',
                'specification' => '27-inch 4K UHD monitor, 3840x2160, USB-C, 99% sRGB',
                'vendor_id' => $vendors->where('name', 'Dell Technologies')->first()->id,
                'purchase_date' => Carbon::now()->subMonths(rand(4, 12)),
                'warranty_period' => Carbon::now()->addYears(3),
                'purchase_price' => 35000.00,
                'created_by' => $users->random()->id,
            ],
            [
                'name' => 'Cisco Meraki MR33 Access Point',
                'category_id' => $categories->where('name', 'Network')->first()->id,
                'location_id' => $locations->random()->id,
                'status' => 'IN USE',
                'model' => 'MR33',
                'specification' => '802.11ac Wave 2, 2.4GHz/5GHz, cloud-managed, PoE+',
                'vendor_id' => $vendors->where('name', 'Cisco Systems')->first()->id ?? $vendors->random()->id,
                'purchase_date' => Carbon::now()->subMonths(rand(10, 24)),
                'warranty_period' => Carbon::now()->addYears(1),
                'purchase_price' => 20000.00,
                'created_by' => $users->random()->id,
            ],
        ];

        foreach ($assets as $assetData) {
            // Calculate derived fields
            $purchaseDate = $assetData['purchase_date'];
            $lifespan = 5; // Default lifespan in years
            $endOfLifeDate = $purchaseDate->copy()->addYears($lifespan);
            $remainingLife = max(0, $endOfLifeDate->diffInYears(Carbon::now(), true));
            
            // Determine life status
            $lifeStatus = 'good';
            if ($remainingLife <= 1) {
                $lifeStatus = 'critical';
            } elseif ($remainingLife <= 2) {
                $lifeStatus = 'warning';
            }

            $assetData['calculated_lifespan'] = $lifespan;
            $assetData['remaining_life'] = round($remainingLife, 2);
            $assetData['end_of_life_date'] = $endOfLifeDate;
            $assetData['life_status'] = $lifeStatus;

            Asset::create($assetData);
        }

        $this->command->info('Created 20 assets with various statuses and configurations.');
    }
}
