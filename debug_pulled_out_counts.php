<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\RepairRequest;
use App\Models\NonRegisteredAsset;

$argUserId = $argv[1] ?? null;

$usersQuery = User::query()->whereIn('group_id', [1, 2]);
if ($argUserId) {
	$usersQuery->where('id', (int)$argUserId);
}
$users = $usersQuery->get();

if ($users->isEmpty()) {
	echo "No matching users found.\n";
	exit(0);
}

foreach ($users as $user) {
	$uid = $user->id;

	$registeredPulledOut = RepairRequest::where('technician_id', $uid)
		->where('status', 'pulled_out')
		->count();

	$nonRegByUserIds = NonRegisteredAsset::where('status', 'PULLED OUT')
		->where('pulled_out_by', $uid)
		->pluck('id');

	$nonRegByTicketIds = NonRegisteredAsset::where('status', 'PULLED OUT')
		->whereHas('repairRequest', function ($q) use ($uid) {
			$q->where('technician_id', $uid);
		})
		->pluck('id');

	$nonRegPulledOutIds = $nonRegByUserIds->merge($nonRegByTicketIds)->unique();
	$nonRegisteredPulledOut = $nonRegPulledOutIds->count();

	$totalPulledOut = $registeredPulledOut + $nonRegisteredPulledOut;

	echo "User: {$user->name} (ID: {$uid})\n";
	echo "  Registered Pulled Out:   {$registeredPulledOut}\n";
	echo "  Non-Registered Pulled Out: {$nonRegisteredPulledOut}\n";
	echo "  Total Pulled Out:        {$totalPulledOut}\n";
	echo str_repeat('-', 40) . "\n";
} 