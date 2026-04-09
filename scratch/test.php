<?php
use Illuminate\Support\Facades\Auth;

Auth::loginUsingId(1);
$perm = App\Models\Permissions::first(); 
$request = Request::create('/test', 'POST', ['action'=>'reject', 'admin_note'=>'Test Reject']);
$response = app(App\Http\Controllers\Admin\AttendancesController::class)->processLeaveRequest($request, $perm->id);
echo json_encode($response->getData());
