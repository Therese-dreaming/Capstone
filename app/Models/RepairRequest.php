<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RepairRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_number',
        'date_called',
        'time_called',
        'building',
        'floor',
        'room',
        'category_id',
        'equipment',
        'serial_number',
        'issue',
        'photo',
        'status',
        'urgency_level',
        'ongoing_activity',
        'technician_id',
        'time_started',
        'completed_at',
        'remarks',
        'caller_name',
        'findings',
        'technician_signature',
        'caller_signature',
        'created_by',
        'urgency_overridden',
        'signature_type',
        'delegate_name',
        'signature_deadline',
        'verification_status',
        'rework_count',
        'before_photos',
        'after_photos',
        'caller_signed_at',
        'reminder_sent_at',
    ];

    protected $casts = [
        'completed_at' => 'datetime',
        'time_started' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'urgency_overridden' => 'boolean',
        'before_photos' => 'array',
        'after_photos' => 'array',
        'signature_deadline' => 'datetime',
        'caller_signed_at' => 'datetime',
        'rework_count' => 'integer',
        'reminder_sent_at' => 'datetime',
    ];

    public function asset()
    {
        return $this->belongsTo(Asset::class, 'serial_number', 'serial_number')
            ->withDefault(['name' => 'Unknown Asset']);
    }
    
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function technician()
    {
        return $this->belongsTo(User::class, 'technician_id');
    }

    public function evaluation()
    {
        return $this->hasOne(TechnicianEvaluation::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function nonRegisteredAssets()
    {
        return $this->hasMany(NonRegisteredAsset::class, 'ticket_number', 'ticket_number');
    }

    public function histories()
    {
        return $this->hasMany(RepairHistory::class)->orderBy('attempt_number', 'asc');
    }
}