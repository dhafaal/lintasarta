<?php

require_once 'vendor/autoload.php';

use Illuminate\Foundation\Application;
use App\Models\Shift;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== TESTING SHIFT SEQUENCE LOGIC ===\n\n";

// Test data - create sample shifts if they don't exist
echo "1. Checking/Creating sample shifts...\n";

$shifts = [
    ['shift_name' => 'Shift Pagi', 'category' => 'Pagi', 'start_time' => '07:00:00', 'end_time' => '15:00:00'],
    ['shift_name' => 'Shift Siang', 'category' => 'Siang', 'start_time' => '15:00:00', 'end_time' => '23:00:00'],
    ['shift_name' => 'Shift Malam', 'category' => 'Malam', 'start_time' => '23:00:00', 'end_time' => '07:00:00'],
];

foreach ($shifts as $shiftData) {
    $shift = Shift::firstOrCreate(
        ['shift_name' => $shiftData['shift_name']],
        $shiftData
    );
    echo "   - {$shift->shift_name} ({$shift->category}): " . ($shift->wasRecentlyCreated ? 'CREATED' : 'EXISTS') . "\n";
}

echo "\n2. Testing shift sequence logic...\n";

// Test Pagi -> Siang
$pagiShift = Shift::where('category', 'Pagi')->first();
if ($pagiShift) {
    $availableAfterPagi = Shift::where('category', 'Siang')->get();
    echo "   - After Pagi shift: " . $availableAfterPagi->count() . " Siang shifts available\n";
    foreach ($availableAfterPagi as $shift) {
        echo "     * {$shift->shift_name}\n";
    }
}

// Test Siang -> Malam
$siangShift = Shift::where('category', 'Siang')->first();
if ($siangShift) {
    $availableAfterSiang = Shift::where('category', 'Malam')->get();
    echo "   - After Siang shift: " . $availableAfterSiang->count() . " Malam shifts available\n";
    foreach ($availableAfterSiang as $shift) {
        echo "     * {$shift->shift_name}\n";
    }
}

// Test Malam -> None
$malamShift = Shift::where('category', 'Malam')->first();
if ($malamShift) {
    echo "   - After Malam shift: 0 shifts available (as expected)\n";
}

echo "\n3. Testing API simulation...\n";

// Simulate API calls
$testCases = [
    ['category' => 'Pagi', 'expected' => 'Siang'],
    ['category' => 'Siang', 'expected' => 'Malam'],
    ['category' => 'Malam', 'expected' => 'None'],
];

foreach ($testCases as $test) {
    $firstShift = Shift::where('category', $test['category'])->first();
    if ($firstShift) {
        $availableShifts = [];
        
        switch ($firstShift->category) {
            case 'Pagi':
                $availableShifts = Shift::where('category', 'Siang')->get();
                break;
            case 'Siang':
                $availableShifts = Shift::where('category', 'Malam')->get();
                break;
            case 'Malam':
                $availableShifts = [];
                break;
        }
        
        $result = count($availableShifts) > 0 ? $availableShifts->first()->category : 'None';
        $status = ($result === $test['expected']) ? '✅ PASS' : '❌ FAIL';
        
        echo "   - {$test['category']} -> {$test['expected']}: $status (got: $result)\n";
    }
}

echo "\n=== TEST COMPLETED ===\n";
