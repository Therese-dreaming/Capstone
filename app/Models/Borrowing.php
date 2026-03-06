<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Borrowing extends Model
{
    protected $fillable = [
        'borrower_id',
        'processed_by',
        'returned_by',
        'purpose',
        'department',
        'borrow_date',
        'expected_return_date',
        'actual_return_date',
        'status',
        'notes',
        'return_notes',
    ];

    protected $casts = [
        'borrow_date' => 'datetime',
        'expected_return_date' => 'date',
        'actual_return_date' => 'datetime',
    ];

    /**
     * Get the borrower (user who is borrowing)
     */
    public function borrower(): BelongsTo
    {
        return $this->belongsTo(User::class, 'borrower_id');
    }

    /**
     * Get the user who processed this borrowing
     */
    public function processedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    /**
     * Get the user who processed the return
     */
    public function returnedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'returned_by');
    }

    /**
     * Get the items in this borrowing
     */
    public function items(): HasMany
    {
        return $this->hasMany(BorrowingItem::class);
    }

    /**
     * Check if borrowing is overdue
     */
    public function isOverdue(): bool
    {
        if ($this->status === 'returned' || $this->status === 'cancelled') {
            return false;
        }
        // Compare only dates, not time - overdue only if past the return date
        return now()->startOfDay()->greaterThan($this->expected_return_date->startOfDay());
    }

    /**
     * Get days until return or days overdue
     */
    public function getDaysRemaining(): int
    {
        if ($this->status === 'returned' || $this->status === 'cancelled') {
            return 0;
        }
        return now()->diffInDays($this->expected_return_date, false);
    }
}
