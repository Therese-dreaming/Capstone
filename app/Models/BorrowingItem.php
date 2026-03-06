<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BorrowingItem extends Model
{
    protected $fillable = [
        'borrowing_id',
        'borrowable_asset_id',
        'condition_on_borrow',
        'condition_on_return',
        'item_notes',
    ];

    /**
     * Get the borrowing this item belongs to
     */
    public function borrowing(): BelongsTo
    {
        return $this->belongsTo(Borrowing::class);
    }

    /**
     * Get the borrowable asset being borrowed
     */
    public function borrowableAsset(): BelongsTo
    {
        return $this->belongsTo(BorrowableAsset::class);
    }

    /**
     * DEPRECATED: Use borrowableAsset() instead
     * Kept for backwards compatibility during transition
     */
    public function asset(): BelongsTo
    {
        return $this->borrowableAsset();
    }
}
