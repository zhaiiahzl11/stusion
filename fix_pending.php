<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$pending = \App\Models\SessionRequest::where('status', 'pending')->get();
foreach($pending as $req) {
    $req->update(['status' => 'assigned']);
    \App\Models\CounselingSession::create([
        'student_id' => $req->student_id,
        'counselor_id' => $req->counselor_id,
        'date' => $req->preferred_date ?? now()->toDateString(),
        'time' => $req->preferred_time ?? '09:00:00',
        'type' => $req->type ?? 'Other',
        'status' => 'assigned'
    ]);
}
echo "Fixed " . $pending->count() . " pending requests.\n";
