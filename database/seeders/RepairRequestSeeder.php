<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RepairRequest;
use App\Models\Asset;
use App\Models\Category;
use App\Models\User;
use Carbon\Carbon;

class RepairRequestSeeder extends Seeder
{
    public function run()
    {
        // Get existing data
        $assets = Asset::whereIn('status', ['IN USE', 'UNDER REPAIR'])->get();
        $categories = Category::all();
        $users = User::all();
        
        $this->command->info('Found ' . $assets->count() . ' assets, ' . $categories->count() . ' categories, ' . $users->count() . ' users');
        
        if ($assets->isEmpty() || $categories->isEmpty() || $users->isEmpty()) {
            $this->command->error('Required data not found. Please run AssetSeeder and other required seeders first.');
            return;
        }

        $repairRequests = [
            // High Priority Issues
            [
                'ticket_number' => 'RR-' . date('Ymd') . '-001',
                'date_called' => Carbon::now()->subDays(1),
                'time_called' => Carbon::now()->subDays(1)->setTime(9, 30),
                'building' => 'Main Building',
                'floor' => '2nd Floor',
                'room' => 'Room 201',
                'category_id' => $categories->where('name', 'Hardware')->first()->id,
                'equipment' => 'Dell OptiPlex 7090 Desktop',
                'serial_number' => $assets->where('name', 'Dell OptiPlex 7090 Desktop')->first()->serial_number ?? 'ASST-20241201-ABCD',
                'issue' => 'Computer not booting up, shows blue screen error on startup. Error code: 0x0000007B',
                'status' => 'pending',
                'urgency_level' => 1, // 1 = high, 2 = medium, 3 = low
                'ongoing_activity' => 'yes',
                'caller_name' => 'Maria Santos',
                'created_by' => $users->random()->id,
            ],
            [
                'ticket_number' => 'RR-' . date('Ymd') . '-002',
                'date_called' => Carbon::now()->subDays(2),
                'time_called' => Carbon::now()->subDays(2)->setTime(14, 15),
                'building' => 'Main Building',
                'floor' => '3rd Floor',
                'room' => 'Room 301',
                'category_id' => $categories->where('name', 'Peripherals')->first()->id,
                'equipment' => 'Canon PIXMA G3110 Printer',
                'serial_number' => $assets->where('name', 'Canon PIXMA G3110 Printer')->first()->serial_number ?? 'ASST-20241201-EFGH',
                'issue' => 'Printer showing paper jam error but no paper is stuck. Print quality is poor with streaks and smudges.',
                'status' => 'in_progress',
                'urgency_level' => 2, // 1 = high, 2 = medium, 3 = low
                'ongoing_activity' => 'yes',
                'technician_id' => $users->where('group_id', 2)->first()->id ?? $users->random()->id,
                'time_started' => Carbon::now()->subDays(1)->setTime(10, 0),
                'caller_name' => 'Juan Dela Cruz',
                'created_by' => $users->random()->id,
            ],
            [
                'ticket_number' => 'RR-' . date('Ymd') . '-003',
                'date_called' => Carbon::now()->subDays(3),
                'time_called' => Carbon::now()->subDays(3)->setTime(11, 45),
                'building' => 'Main Building',
                'floor' => '1st Floor',
                'room' => 'Room 101',
                'category_id' => $categories->where('name', 'Network')->first()->id,
                'equipment' => 'Cisco Catalyst 2960 Switch',
                'serial_number' => $assets->where('name', 'Cisco Catalyst 2960 Switch')->first()->serial_number ?? 'ASST-20241201-IJKL',
                'issue' => 'Network connectivity issues in the lab. Some computers cannot access the internet and local network resources.',
                'status' => 'completed',
                'urgency_level' => 1, // 1 = high, 2 = medium, 3 = low
                'ongoing_activity' => 'yes',
                'technician_id' => $users->where('group_id', 2)->first()->id ?? $users->random()->id,
                'time_started' => Carbon::now()->subDays(2)->setTime(9, 0),
                'completed_at' => Carbon::now()->subDays(1)->setTime(16, 30),
                'remarks' => 'Replaced faulty network cable and reconfigured switch ports. All connections restored.',
                'findings' => 'Found damaged network cable causing intermittent connectivity. Port configuration was also corrupted.',
                'caller_name' => 'Ana Rodriguez',
                'created_by' => $users->random()->id,
            ],
            [
                'ticket_number' => 'RR-' . date('Ymd') . '-004',
                'date_called' => Carbon::now()->subDays(4),
                'time_called' => Carbon::now()->subDays(4)->setTime(8, 20),
                'building' => 'Main Building',
                'floor' => '2nd Floor',
                'room' => 'Room 205',
                'category_id' => $categories->where('name', 'Hardware')->first()->id,
                'equipment' => 'HP EliteBook 850 G8 Laptop',
                'serial_number' => $assets->where('name', 'HP EliteBook 850 G8 Laptop')->first()->serial_number ?? 'ASST-20241201-MNOP',
                'issue' => 'Laptop screen flickering and showing horizontal lines. Battery not holding charge properly.',
                'status' => 'pending',
                'urgency_level' => 2, // 1 = high, 2 = medium, 3 = low
                'ongoing_activity' => 'yes',
                'caller_name' => 'Carlos Mendoza',
                'created_by' => $users->random()->id,
            ],
            [
                'ticket_number' => 'RR-' . date('Ymd') . '-005',
                'date_called' => Carbon::now()->subDays(5),
                'time_called' => Carbon::now()->subDays(5)->setTime(13, 30),
                'building' => 'Main Building',
                'floor' => '3rd Floor',
                'room' => 'Room 305',
                'category_id' => $categories->where('name', 'Peripherals')->first()->id,
                'equipment' => 'Epson WorkForce Pro WF-3720',
                'serial_number' => $assets->where('name', 'Epson WorkForce Pro WF-3720')->first()->serial_number ?? 'ASST-20241201-QRST',
                'issue' => 'Printer showing "Ink cartridge not recognized" error. Print head appears to be clogged.',
                'status' => 'in_progress',
                'urgency_level' => 3, // 1 = high, 2 = medium, 3 = low
                'ongoing_activity' => 'Administrative office printing needs affected',
                'technician_id' => $users->where('group_id', 2)->first()->id ?? $users->random()->id,
                'time_started' => Carbon::now()->subDays(3)->setTime(14, 0),
                'caller_name' => 'Lisa Garcia',
                'created_by' => $users->random()->id,
            ],
            [
                'ticket_number' => 'RR-' . date('Ymd') . '-006',
                'date_called' => Carbon::now()->subDays(6),
                'time_called' => Carbon::now()->subDays(6)->setTime(10, 15),
                'building' => 'Main Building',
                'floor' => '1st Floor',
                'room' => 'Room 105',
                'category_id' => $categories->where('name', 'Hardware')->first()->id,
                'equipment' => 'Lenovo ThinkPad X1 Carbon',
                'serial_number' => $assets->where('name', 'Lenovo ThinkPad X1 Carbon')->first()->serial_number ?? 'ASST-20241201-UVWX',
                'issue' => 'Laptop keyboard not responding properly. Some keys are stuck and others are not registering input.',
                'status' => 'completed',
                'urgency_level' => 1, // 1 = high, 2 = medium, 3 = low
                'ongoing_activity' => 'Student unable to complete programming assignments',
                'technician_id' => $users->where('group_id', 2)->first()->id ?? $users->random()->id,
                'time_started' => Carbon::now()->subDays(5)->setTime(9, 0),
                'completed_at' => Carbon::now()->subDays(4)->setTime(15, 45),
                'remarks' => 'Replaced keyboard assembly and updated drivers. All keys now functioning properly.',
                'findings' => 'Keyboard membrane was damaged due to liquid spill. Required complete keyboard replacement.',
                'caller_name' => 'Student John Smith',
                'created_by' => $users->random()->id,
            ],
            [
                'ticket_number' => 'RR-' . date('Ymd') . '-007',
                'date_called' => Carbon::now()->subDays(7),
                'time_called' => Carbon::now()->subDays(7)->setTime(15, 45),
                'building' => 'Main Building',
                'floor' => '2nd Floor',
                'room' => 'Room 210',
                'category_id' => $categories->where('name', 'Peripherals')->first()->id,
                'equipment' => 'Logitech C920 HD Pro Webcam',
                'serial_number' => $assets->where('name', 'Logitech C920 HD Pro Webcam')->first()->serial_number ?? 'ASST-20241201-YZAB',
                'issue' => 'Webcam not being detected by the computer. Shows as unknown device in device manager.',
                'status' => 'pending',
                'urgency_level' => 2, // 1 = high, 2 = medium, 3 = low
                'ongoing_activity' => 'Online meeting setup delayed',
                'caller_name' => 'Prof. Maria Lopez',
                'created_by' => $users->random()->id,
            ],
            [
                'ticket_number' => 'RR-' . date('Ymd') . '-008',
                'date_called' => Carbon::now()->subDays(8),
                'time_called' => Carbon::now()->subDays(8)->setTime(12, 0),
                'building' => 'Main Building',
                'floor' => '3rd Floor',
                'room' => 'Room 310',
                'category_id' => $categories->where('name', 'Hardware')->first()->id,
                'equipment' => 'ASUS VivoBook S15',
                'serial_number' => $assets->where('name', 'ASUS VivoBook S15')->first()->serial_number ?? 'ASST-20241201-CDEF',
                'issue' => 'Laptop overheating and shutting down randomly during heavy usage. Fan making loud noise.',
                'status' => 'in_progress',
                'urgency_level' => 1, // 1 = high, 2 = medium, 3 = low
                'ongoing_activity' => 'Student unable to complete video editing project',
                'technician_id' => $users->where('group_id', 2)->first()->id ?? $users->random()->id,
                'time_started' => Carbon::now()->subDays(6)->setTime(11, 0),
                'caller_name' => 'Student Sarah Johnson',
                'created_by' => $users->random()->id,
            ],
            [
                'ticket_number' => 'RR-' . date('Ymd') . '-009',
                'date_called' => Carbon::now()->subDays(9),
                'time_called' => Carbon::now()->subDays(9)->setTime(9, 30),
                'building' => 'Main Building',
                'floor' => '1st Floor',
                'room' => 'Room 110',
                'category_id' => $categories->where('name', 'Network')->first()->id,
                'equipment' => 'TP-Link Archer C7 Router',
                'serial_number' => $assets->where('name', 'TP-Link Archer C7 Router')->first()->serial_number ?? 'ASST-20241201-GHIJ',
                'issue' => 'WiFi signal is very weak in the area. Router lights are blinking irregularly.',
                'status' => 'completed',
                'urgency_level' => 2, // 1 = high, 2 = medium, 3 = low
                'ongoing_activity' => 'Students having trouble connecting to WiFi for online classes',
                'technician_id' => $users->where('group_id', 2)->first()->id ?? $users->random()->id,
                'time_started' => Carbon::now()->subDays(8)->setTime(10, 0),
                'completed_at' => Carbon::now()->subDays(7)->setTime(14, 30),
                'remarks' => 'Reset router to factory settings and updated firmware. Signal strength improved significantly.',
                'findings' => 'Router firmware was outdated and configuration was corrupted. Required complete reset.',
                'caller_name' => 'Engr. Roberto Silva',
                'created_by' => $users->random()->id,
            ],
            [
                'ticket_number' => 'RR-' . date('Ymd') . '-010',
                'date_called' => Carbon::now()->subDays(10),
                'time_called' => Carbon::now()->subDays(10)->setTime(16, 20),
                'building' => 'Main Building',
                'floor' => '2nd Floor',
                'room' => 'Room 220',
                'category_id' => $categories->where('name', 'Peripherals')->first()->id,
                'equipment' => 'Samsung 24-inch Monitor',
                'serial_number' => $assets->where('name', 'Samsung 24-inch Monitor')->first()->serial_number ?? 'ASST-20241201-KLMN',
                'issue' => 'Monitor showing distorted colors and horizontal lines. Display flickers occasionally.',
                'status' => 'pending',
                'urgency_level' => 3, // 1 = high, 2 = medium, 3 = low
                'ongoing_activity' => 'Workstation temporarily unavailable',
                'caller_name' => 'Prof. Elena Torres',
                'created_by' => $users->random()->id,
            ],
            [
                'ticket_number' => 'RR-' . date('Ymd') . '-011',
                'date_called' => Carbon::now()->subDays(11),
                'time_called' => Carbon::now()->subDays(11)->setTime(11, 10),
                'building' => 'Main Building',
                'floor' => '3rd Floor',
                'room' => 'Room 315',
                'category_id' => $categories->where('name', 'Hardware')->first()->id,
                'equipment' => 'MacBook Pro 13-inch',
                'serial_number' => $assets->where('name', 'MacBook Pro 13-inch')->first()->serial_number ?? 'ASST-20241201-OPQR',
                'issue' => 'MacBook not charging properly. Battery drains quickly even when plugged in.',
                'status' => 'in_progress',
                'urgency_level' => 1, // 1 = high, 2 = medium, 3 = low
                'ongoing_activity' => 'Faculty member unable to use laptop for presentations',
                'technician_id' => $users->where('group_id', 2)->first()->id ?? $users->random()->id,
                'time_started' => Carbon::now()->subDays(9)->setTime(13, 0),
                'caller_name' => 'Dr. Michael Chen',
                'created_by' => $users->random()->id,
            ],
            [
                'ticket_number' => 'RR-' . date('Ymd') . '-012',
                'date_called' => Carbon::now()->subDays(12),
                'time_called' => Carbon::now()->subDays(12)->setTime(14, 35),
                'building' => 'Main Building',
                'floor' => '1st Floor',
                'room' => 'Room 115',
                'category_id' => $categories->where('name', 'Hardware')->first()->id,
                'equipment' => 'Lenovo ThinkCentre M720',
                'serial_number' => $assets->where('name', 'Lenovo ThinkCentre M720')->first()->serial_number ?? 'ASST-20241201-STUV',
                'issue' => 'Desktop computer running very slowly. Takes 10+ minutes to boot up and applications freeze frequently.',
                'status' => 'completed',
                'urgency_level' => 2, // 1 = high, 2 = medium, 3 = low
                'ongoing_activity' => 'Administrative tasks delayed due to slow performance',
                'technician_id' => $users->where('group_id', 2)->first()->id ?? $users->random()->id,
                'time_started' => Carbon::now()->subDays(11)->setTime(9, 0),
                'completed_at' => Carbon::now()->subDays(10)->setTime(16, 0),
                'remarks' => 'Cleaned up disk space, removed malware, and optimized system. Performance significantly improved.',
                'findings' => 'Hard drive was 95% full and infected with malware. Required complete system cleanup and optimization.',
                'caller_name' => 'Ms. Patricia Reyes',
                'created_by' => $users->random()->id,
            ],
            [
                'ticket_number' => 'RR-' . date('Ymd') . '-013',
                'date_called' => Carbon::now()->subDays(13),
                'time_called' => Carbon::now()->subDays(13)->setTime(10, 45),
                'building' => 'Main Building',
                'floor' => '2nd Floor',
                'room' => 'Room 225',
                'category_id' => $categories->where('name', 'Hardware')->first()->id,
                'equipment' => 'ASUS ROG Strix G15 Gaming Laptop',
                'serial_number' => $assets->where('name', 'ASUS ROG Strix G15 Gaming Laptop')->first()->serial_number ?? 'ASST-20241201-WXYZ',
                'issue' => 'Gaming laptop experiencing random crashes during intensive tasks. Blue screen errors with memory dump.',
                'status' => 'pending',
                'urgency_level' => 1, // 1 = high, 2 = medium, 3 = low
                'ongoing_activity' => 'Student unable to complete 3D modeling project',
                'caller_name' => 'Student David Kim',
                'created_by' => $users->random()->id,
            ],
            [
                'ticket_number' => 'RR-' . date('Ymd') . '-014',
                'date_called' => Carbon::now()->subDays(14),
                'time_called' => Carbon::now()->subDays(14)->setTime(13, 20),
                'building' => 'Main Building',
                'floor' => '3rd Floor',
                'room' => 'Room 320',
                'category_id' => $categories->where('name', 'Peripherals')->first()->id,
                'equipment' => 'Dell UltraSharp U2720Q Monitor',
                'serial_number' => $assets->where('name', 'Dell UltraSharp U2720Q Monitor')->first()->serial_number ?? 'ASST-20241201-ABCD',
                'issue' => '4K monitor showing resolution issues. Text appears blurry and colors are not accurate.',
                'status' => 'in_progress',
                'urgency_level' => 2, // 1 = high, 2 = medium, 3 = low
                'ongoing_activity' => 'Design work affected by display quality issues',
                'technician_id' => $users->where('group_id', 2)->first()->id ?? $users->random()->id,
                'time_started' => Carbon::now()->subDays(12)->setTime(14, 0),
                'caller_name' => 'Prof. Amanda Wilson',
                'created_by' => $users->random()->id,
            ],
            [
                'ticket_number' => 'RR-' . date('Ymd') . '-015',
                'date_called' => Carbon::now()->subDays(15),
                'time_called' => Carbon::now()->subDays(15)->setTime(15, 50),
                'building' => 'Main Building',
                'floor' => '1st Floor',
                'room' => 'Room 120',
                'category_id' => $categories->where('name', 'Network')->first()->id,
                'equipment' => 'Cisco Meraki MR33 Access Point',
                'serial_number' => $assets->where('name', 'Cisco Meraki MR33 Access Point')->first()->serial_number ?? 'ASST-20241201-EFGH',
                'issue' => 'WiFi access point not broadcasting signal. Status light shows offline.',
                'status' => 'completed',
                'urgency_level' => 1, // 1 = high, 2 = medium, 3 = low
                'ongoing_activity' => 'Entire floor without WiFi access',
                'technician_id' => $users->where('group_id', 2)->first()->id ?? $users->random()->id,
                'time_started' => Carbon::now()->subDays(14)->setTime(8, 0),
                'completed_at' => Carbon::now()->subDays(13)->setTime(12, 0),
                'remarks' => 'Replaced faulty access point and reconfigured network settings. WiFi coverage restored.',
                'findings' => 'Access point hardware failure. Power supply and network configuration were also affected.',
                'caller_name' => 'Engr. James Martinez',
                'created_by' => $users->random()->id,
            ],
        ];

        foreach ($repairRequests as $requestData) {
            // Fix ongoing_activity field to be 'yes' or 'no'
            if (isset($requestData['ongoing_activity']) && strlen($requestData['ongoing_activity']) > 2) {
                $requestData['ongoing_activity'] = 'yes';
            }
            RepairRequest::create($requestData);
        }

        $this->command->info('Created 15 repair requests with various statuses and urgency levels.');
    }
}
