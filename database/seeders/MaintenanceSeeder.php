<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Maintenance;
use App\Models\Location;
use App\Models\User;
use Carbon\Carbon;

class MaintenanceSeeder extends Seeder
{
    public function run()
    {
        // Get existing data
        $locations = Location::all();
        $users = User::all();
        
        $this->command->info('Found ' . $locations->count() . ' locations, ' . $users->count() . ' users');
        
        if ($locations->isEmpty() || $users->isEmpty()) {
            $this->command->error('Required data not found. Please run LaboratorySeeder and CreateDefaultUserSeeder first.');
            return;
        }

        $maintenanceTasks = [
            // Completed Maintenance Tasks
            [
                'location_id' => $locations->random()->id,
                'maintenance_task' => 'Monthly Computer System Check',
                'technician_id' => $users->where('group_id', 2)->first()->id ?? $users->random()->id,
                'scheduled_date' => Carbon::now()->subDays(30),
                'target_date' => Carbon::now()->subDays(30)->addDays(1),
                'status' => 'completed',
                'action_by_id' => $users->where('group_id', 2)->first()->id ?? $users->random()->id,
                'completed_at' => Carbon::now()->subDays(29),
                'excluded_assets' => [],
                'asset_issues' => [
                    'Dell OptiPlex 7090 - Minor dust buildup in CPU fan',
                    'HP EliteBook 850 G8 - Battery health at 85%',
                    'Lenovo ThinkPad X1 Carbon - Keyboard cleaning needed'
                ],
                'notes' => 'Routine maintenance completed successfully. All systems functioning properly. Minor cleaning performed on identified assets.',
                'serial_number' => null,
            ],
            [
                'location_id' => $locations->random()->id,
                'maintenance_task' => 'Network Equipment Inspection',
                'technician_id' => $users->where('group_id', 2)->first()->id ?? $users->random()->id,
                'scheduled_date' => Carbon::now()->subDays(45),
                'target_date' => Carbon::now()->subDays(45)->addDays(2),
                'status' => 'completed',
                'action_by_id' => $users->where('group_id', 2)->first()->id ?? $users->random()->id,
                'completed_at' => Carbon::now()->subDays(43),
                'excluded_assets' => [],
                'asset_issues' => [
                    'Cisco Catalyst 2960 Switch - Port 12 showing intermittent connectivity',
                    'TP-Link Archer C7 Router - Firmware update available',
                    'Cisco Meraki MR33 - Signal strength optimal'
                ],
                'notes' => 'Network infrastructure inspection completed. Updated firmware on router. Port 12 issue resolved by replacing network cable.',
                'serial_number' => null,
            ],
            [
                'location_id' => $locations->random()->id,
                'maintenance_task' => 'Printer Maintenance and Cleaning',
                'technician_id' => $users->where('group_id', 2)->first()->id ?? $users->random()->id,
                'scheduled_date' => Carbon::now()->subDays(60),
                'target_date' => Carbon::now()->subDays(60)->addDays(1),
                'status' => 'completed',
                'action_by_id' => $users->where('group_id', 2)->first()->id ?? $users->random()->id,
                'completed_at' => Carbon::now()->subDays(59),
                'excluded_assets' => [],
                'asset_issues' => [
                    'Canon PIXMA G3110 - Print head cleaning required',
                    'Epson WorkForce Pro WF-3720 - Paper feed mechanism needs lubrication',
                    'HP LaserJet Pro M404n - Toner cartridge replacement needed'
                ],
                'notes' => 'All printers cleaned and serviced. Print heads cleaned, paper feed mechanisms lubricated. Toner cartridges replaced where necessary.',
                'serial_number' => null,
            ],
            [
                'location_id' => $locations->random()->id,
                'maintenance_task' => 'Software Updates and Security Patches',
                'technician_id' => $users->where('group_id', 2)->first()->id ?? $users->random()->id,
                'scheduled_date' => Carbon::now()->subDays(15),
                'target_date' => Carbon::now()->subDays(15)->addDays(3),
                'status' => 'completed',
                'action_by_id' => $users->where('group_id', 2)->first()->id ?? $users->random()->id,
                'completed_at' => Carbon::now()->subDays(12),
                'excluded_assets' => [],
                'asset_issues' => [
                    'Windows 11 Pro - Security updates installed',
                    'Adobe Creative Cloud - Software updated to latest version',
                    'Microsoft Office 365 - License validation completed'
                ],
                'notes' => 'All software updates and security patches applied successfully. System performance improved after updates.',
                'serial_number' => null,
            ],
            [
                'location_id' => $locations->random()->id,
                'maintenance_task' => 'Hardware Performance Optimization',
                'technician_id' => $users->where('group_id', 2)->first()->id ?? $users->random()->id,
                'scheduled_date' => Carbon::now()->subDays(20),
                'target_date' => Carbon::now()->subDays(20)->addDays(2),
                'status' => 'completed',
                'action_by_id' => $users->where('group_id', 2)->first()->id ?? $users->random()->id,
                'completed_at' => Carbon::now()->subDays(18),
                'excluded_assets' => [],
                'asset_issues' => [
                    'Lenovo ThinkCentre M720 - RAM upgrade from 8GB to 16GB',
                    'ASUS VivoBook S15 - SSD optimization and defragmentation',
                    'MacBook Pro 13-inch - Battery calibration performed'
                ],
                'notes' => 'Hardware optimization completed. RAM upgrade significantly improved system performance. SSD optimization completed successfully.',
                'serial_number' => null,
            ],

            // In Progress Maintenance Tasks
            [
                'location_id' => $locations->random()->id,
                'maintenance_task' => 'Quarterly System Deep Clean',
                'technician_id' => $users->where('group_id', 2)->first()->id ?? $users->random()->id,
                'scheduled_date' => Carbon::now()->subDays(5),
                'target_date' => Carbon::now()->addDays(2),
                'status' => 'in_progress',
                'action_by_id' => $users->where('group_id', 2)->first()->id ?? $users->random()->id,
                'completed_at' => null,
                'excluded_assets' => [],
                'asset_issues' => [
                    'Dell OptiPlex 7090 - CPU thermal paste replacement needed',
                    'HP EliteBook 850 G8 - Keyboard deep cleaning in progress',
                    'Samsung 24-inch Monitor - Screen calibration required'
                ],
                'notes' => 'Deep cleaning in progress. Thermal paste replacement completed. Keyboard cleaning ongoing. Screen calibration scheduled for tomorrow.',
                'serial_number' => null,
            ],
            [
                'location_id' => $locations->random()->id,
                'maintenance_task' => 'Network Security Audit',
                'technician_id' => $users->where('group_id', 2)->first()->id ?? $users->random()->id,
                'scheduled_date' => Carbon::now()->subDays(3),
                'target_date' => Carbon::now()->addDays(5),
                'status' => 'in_progress',
                'action_by_id' => $users->where('group_id', 2)->first()->id ?? $users->random()->id,
                'completed_at' => null,
                'excluded_assets' => [],
                'asset_issues' => [
                    'Cisco Catalyst 2960 Switch - Security configuration review in progress',
                    'TP-Link Archer C7 Router - Password policy update needed',
                    'Cisco Meraki MR33 - Access control list review required'
                ],
                'notes' => 'Security audit in progress. Switch configuration reviewed. Router password policy updated. Access control list review ongoing.',
                'serial_number' => null,
            ],

            // Scheduled Maintenance Tasks
            [
                'location_id' => $locations->random()->id,
                'maintenance_task' => 'Annual Hardware Inventory and Assessment',
                'technician_id' => $users->where('group_id', 2)->first()->id ?? $users->random()->id,
                'scheduled_date' => Carbon::now()->addDays(7),
                'target_date' => Carbon::now()->addDays(14),
                'status' => 'scheduled',
                'action_by_id' => null,
                'completed_at' => null,
                'excluded_assets' => [],
                'asset_issues' => [],
                'notes' => 'Annual inventory scheduled. Will assess all hardware for performance, warranty status, and replacement needs.',
                'serial_number' => null,
            ],
            [
                'location_id' => $locations->random()->id,
                'maintenance_task' => 'Software License Renewal and Validation',
                'technician_id' => $users->where('group_id', 2)->first()->id ?? $users->random()->id,
                'scheduled_date' => Carbon::now()->addDays(10),
                'target_date' => Carbon::now()->addDays(17),
                'status' => 'scheduled',
                'action_by_id' => null,
                'completed_at' => null,
                'excluded_assets' => [],
                'asset_issues' => [],
                'notes' => 'Software license renewal process scheduled. Will validate all software licenses and renew expiring ones.',
                'serial_number' => null,
            ],
            [
                'location_id' => $locations->random()->id,
                'maintenance_task' => 'Backup System Verification and Testing',
                'technician_id' => $users->where('group_id', 2)->first()->id ?? $users->random()->id,
                'scheduled_date' => Carbon::now()->addDays(14),
                'target_date' => Carbon::now()->addDays(21),
                'status' => 'scheduled',
                'action_by_id' => null,
                'completed_at' => null,
                'excluded_assets' => [],
                'asset_issues' => [],
                'notes' => 'Backup system verification scheduled. Will test backup procedures and data recovery processes.',
                'serial_number' => null,
            ],

            // Overdue Maintenance Tasks
            [
                'location_id' => $locations->random()->id,
                'maintenance_task' => 'Monitor Calibration and Color Accuracy Check',
                'technician_id' => $users->where('group_id', 2)->first()->id ?? $users->random()->id,
                'scheduled_date' => Carbon::now()->subDays(20),
                'target_date' => Carbon::now()->subDays(15),
                'status' => 'overdue',
                'action_by_id' => $users->where('group_id', 2)->first()->id ?? $users->random()->id,
                'completed_at' => null,
                'excluded_assets' => [],
                'asset_issues' => [
                    'Dell UltraSharp U2720Q - Color calibration overdue',
                    'Samsung 24-inch Monitor - Brightness adjustment needed',
                    'ASUS VivoBook S15 - Display color accuracy check required'
                ],
                'notes' => 'Monitor calibration overdue. Color accuracy issues reported by users. Priority task for design workstations.',
                'serial_number' => null,
            ],
            [
                'location_id' => $locations->random()->id,
                'maintenance_task' => 'UPS Battery Replacement and Testing',
                'technician_id' => $users->where('group_id', 2)->first()->id ?? $users->random()->id,
                'scheduled_date' => Carbon::now()->subDays(30),
                'target_date' => Carbon::now()->subDays(25),
                'status' => 'overdue',
                'action_by_id' => $users->where('group_id', 2)->first()->id ?? $users->random()->id,
                'completed_at' => null,
                'excluded_assets' => [],
                'asset_issues' => [
                    'UPS Unit 1 - Battery capacity at 60%, replacement needed',
                    'UPS Unit 2 - Battery test failed, immediate replacement required',
                    'UPS Unit 3 - Battery backup time reduced to 15 minutes'
                ],
                'notes' => 'UPS battery replacement overdue. Critical for power protection. Multiple units showing battery degradation.',
                'serial_number' => null,
            ],

            // Cancelled Maintenance Tasks
            [
                'location_id' => $locations->random()->id,
                'maintenance_task' => 'Legacy System Migration',
                'technician_id' => $users->where('group_id', 2)->first()->id ?? $users->random()->id,
                'scheduled_date' => Carbon::now()->subDays(10),
                'target_date' => Carbon::now()->subDays(5),
                'status' => 'cancelled',
                'action_by_id' => $users->where('group_id', 2)->first()->id ?? $users->random()->id,
                'completed_at' => null,
                'excluded_assets' => [],
                'asset_issues' => [],
                'notes' => 'Migration cancelled due to budget constraints. Will be rescheduled for next quarter.',
                'serial_number' => null,
            ],
        ];

        foreach ($maintenanceTasks as $maintenanceData) {
            Maintenance::create($maintenanceData);
        }

        $this->command->info('Created 12 maintenance tasks with various statuses and schedules.');
    }
}
