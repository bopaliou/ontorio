<?php
use App\Models\User;
use Illuminate\Support\Facades\Hash;

echo "--- User List ---" . PHP_EOL;
User::all()->each(function($u) {
    $check = Hash::check('password', $u->password) ? 'OK' : 'FAIL';
    echo "{$u->email} | {$u->role} | Password: {$check}" . PHP_EOL;
});
echo "-----------------" . PHP_EOL;
