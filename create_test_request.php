<?php

// Script to create a test completed request WITHOUT a caller signature
require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\RepairRequest;
use Illuminate\Support\Str;

echo "=== CREATING TEST REQUEST WITHOUT SIGNATURE ===\n\n";

// Create a test request
$request = new RepairRequest();
$request->ticket_number = 'TEST-' . date('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
$request->building = 'Test Building';
$request->floor = 'Ground Floor';
$request->room = 'Room 101';
$request->equipment = 'Test Equipment';
$request->issue = 'Test issue for signature indicator';
$request->status = 'completed';
$request->completed_at = now();
$request->time_started = now()->subHours(2);
$request->caller_name = 'Test User';
$request->findings = 'Test findings';
$request->remarks = 'Test remarks';
$request->created_by = 1; // Assuming user ID 1 exists
$request->technician_id = 1; // Assuming user ID 1 exists

// IMPORTANT: Leave caller_signature as NULL
$request->caller_signature = null;

// Add a technician signature (optional)
$request->technician_signature = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==';

$request->save();

echo "✓ Created test request:\n";
echo "  ID: {$request->id}\n";
echo "  Ticket: {$request->ticket_number}\n";
echo "  Status: {$request->status}\n";
echo "  Caller Signature: " . ($request->caller_signature ? 'HAS SIGNATURE' : 'NULL (NO SIGNATURE)') . "\n";
echo "\n";
echo "The 'No Signature' indicator should now appear for this request!\n";
echo "Go to the Repair Requests History page to see it.\n";
echo "\n=== DONE ===\n";
