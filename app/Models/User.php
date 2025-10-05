<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Carbon\Carbon;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'name',
        'username',
        'rfid_number',
        'password',
        'department',
        'position',
        'group_id',
        'status',
        'gender'
    ];

    protected $casts = [
        'last_login' => 'datetime'
    ];

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    // Add this relationship
    public function labLogs()
    {
        return $this->hasMany(LabLog::class);
    }

    // Repair request relationships
    public function repairRequestsAsTechnician()
    {
        return $this->hasMany(RepairRequest::class, 'technician_id');
    }

    public function repairRequestsAsCreator()
    {
        return $this->hasMany(RepairRequest::class, 'created_by');
    }

    // Asset relationships
    public function assetsAsCreator()
    {
        return $this->hasMany(Asset::class, 'created_by');
    }

    // Maintenance relationships
    public function maintenanceAsTechnician()
    {
        return $this->hasMany(Maintenance::class, 'technician_id');
    }

    public function maintenanceAsScheduler()
    {
        return $this->hasMany(Maintenance::class, 'action_by_id');
    }

    // Asset history relationship
    public function assetHistories()
    {
        return $this->hasMany(AssetHistory::class, 'changed_by');
    }

    // Technician evaluation relationships
    public function technicianEvaluations()
    {
        return $this->hasMany(TechnicianEvaluation::class, 'technician_id');
    }

    public function evaluatorEvaluations()
    {
        return $this->hasMany(TechnicianEvaluation::class, 'evaluator_id');
    }

    /**
     * Get the appropriate default profile picture based on gender
     */
    public function getDefaultProfilePicture()
    {
        if ($this->gender === 'male') {
            return asset('images/default-profile-male.png');
        } elseif ($this->gender === 'female') {
            return asset('images/default-profile.png');
        }
        
        // Default fallback for null gender
        return asset('images/default-profile.png');
    }

    /**
     * Get the profile picture URL, using default if none is set
     */
    public function getProfilePictureUrl()
    {
        return $this->profile_picture ? asset($this->profile_picture) : $this->getDefaultProfilePicture();
    }

    /**
     * Get navigation counts for the sidebar
     */
    public function getNavigationCounts()
    {
        $counts = [];

        // Lab Maintenance counts
        if ($this->group_id !== 4) { // Not custodian
            $maintenanceQuery = Maintenance::where('status', 'scheduled');
            
            // If user is secretary (group_id = 2), only show their assigned tasks
            if ($this->group_id === 2) {
                $maintenanceQuery->where('technician_id', $this->id);
            }
            
            $counts['maintenance_scheduled'] = $maintenanceQuery->count();
        }

        // Asset Repair counts
        if ($this->group_id !== 3) { // Not technician
            $repairQuery = RepairRequest::whereIn('status', ['pending', 'in_progress']);
            
            // If user is secretary (group_id = 2), only show their assigned tasks
            if ($this->group_id === 2) {
                $repairQuery->where('technician_id', $this->id);
            }
            
            $counts['repair_pending'] = $repairQuery->count();
        }

        // Asset List - Warranty counts (expired and expiring)
        if ($this->group_id !== 3) { // Not technician
            $today = Carbon::now();
            $thirtyDaysFromNow = $today->copy()->addDays(30);
            
            // Expired warranties (past due)
            $expiredQuery = Asset::where('status', '!=', 'DISPOSED')
                ->where('warranty_period', '<', $today->format('Y-m-d'));
            
            $counts['warranty_expired'] = $expiredQuery->count();
            
            // Expiring warranties (within next 30 days, but not yet expired)
            $expiringQuery = Asset::where('status', '!=', 'DISPOSED')
                ->whereBetween('warranty_period', [$today->format('Y-m-d'), $thirtyDaysFromNow->format('Y-m-d')]);
            
            $counts['warranty_expiring'] = $expiringQuery->count();
        }

        // Non-Registered Assets - Pulled out assets that are still not registered
        if ($this->group_id !== 3) { // Not technician
            $pulledOutQuery = NonRegisteredAsset::where('status', 'PULLED OUT')
                ->whereNull('linked_asset_id'); // Exclude assets that have been linked to registered assets
            
            $counts['non_registered_pulled_out'] = $pulledOutQuery->count();
        }

        // Lab Schedule counts (if applicable)
        if ($this->group_id !== 3 && $this->group_id !== 4) { // Not technician or custodian
            // Add lab schedule counts if needed
            $counts['lab_schedule'] = 0; // Placeholder for future implementation
        }

        return $counts;
    }

    /**
     * Get specific count for a navigation item
     */
    public function getNavigationCount($type)
    {
        $counts = $this->getNavigationCounts();
        return $counts[$type] ?? 0;
    }

    /**
     * Get total warranty issues count (expired + expiring)
     */
    public function getTotalWarrantyIssues()
    {
        return $this->getNavigationCount('warranty_expired') + $this->getNavigationCount('warranty_expiring');
    }
}
