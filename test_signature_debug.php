<?php

// Test script to debug caller_signature field
require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\RepairRequest;

echo "=== REPAIR REQUEST SIGNATURE DEBUG ===\n\n";

// Get all completed requests
$completedRequests = RepairRequest::whereIn('status', ['completed', 'cancelled', 'pulled_out'])
    ->orderBy('updated_at', 'desc')
    ->limit(10)
    ->get();

echo "Found " . $completedRequests->count() . " completed/cancelled/pulled_out requests\n\n";

foreach ($completedRequests as $request) {
    echo "-----------------------------------\n";
    echo "ID: {$request->id}\n";
    echo "Ticket: {$request->ticket_number}\n";
    echo "Status: {$request->status}\n";
    echo "Caller Signature Field:\n";
    
    if ($request->caller_signature === null) {
        echo "  Value: NULL\n";
        echo "  Type: NULL\n";
        echo "  Empty Check: " . (empty($request->caller_signature) ? 'TRUE' : 'FALSE') . "\n";
        echo "  Is Null Check: TRUE\n";
    } else {
        echo "  Value: " . (strlen($request->caller_signature) > 100 ? substr($request->caller_signature, 0, 100) . '...' : $request->caller_signature) . "\n";
        echo "  Type: " . gettype($request->caller_signature) . "\n";
        echo "  Length: " . strlen($request->caller_signature) . "\n";
        echo "  Empty Check: " . (empty($request->caller_signature) ? 'TRUE' : 'FALSE') . "\n";
        echo "  Is Null Check: FALSE\n";
        echo "  Trim Empty: " . (trim($request->caller_signature) === '' ? 'TRUE' : 'FALSE') . "\n";
        echo "  Equals 'null': " . ($request->caller_signature === 'null' ? 'TRUE' : 'FALSE') . "\n";
    }
    
    // Check the condition used in the view
    $shouldShowIndicator = !$request->caller_signature || trim($request->caller_signature) === '' || $request->caller_signature === 'null';
    echo "  SHOULD SHOW 'No Signature' INDICATOR: " . ($shouldShowIndicator ? 'YES' : 'NO') . "\n";
    
    echo "\n";
}

echo "=== END DEBUG ===\n";
