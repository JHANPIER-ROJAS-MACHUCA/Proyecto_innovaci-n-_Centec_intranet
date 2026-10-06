<?php

namespace CrediSoporte\Modules\Goals\Legacy;

use CrediSoporte\Domain\Models\Goal;
use CrediSoporte\Domain\Models\User;
use CrediSoporte\Domain\Request\Request;

// Logica migrada verbatim desde app/api/createMeta.php.
// El wrapper en app/api/createMeta.php preserva URL, entradas y salida legacy.
class CreateMeta
{
    public static function handle(): void
    {$request = new Request();

$errors = $request->validate([
    'user_id' => 'required|exists',
    'date' => 'required|date|unique',
    'saldo' => 'required|numeric|min:1',
    'operation' => 'required|numeric|min:1'
], [], [
    'user_id.exists' => User::where('idU', $request->user_id)->exists(),
    'date.unique' => Goal::where('user_id', $request->user_id)
        ->whereRaw("? between start_at and end_at", [$request->date])
        ->exists()
]);

if (count($errors) > 0) {
    http_response_code(422);
    echo json_encode([
        'errors' => $errors,
        'success' => false
    ]);
    die();
}

$dp = explode('-', $request->date);
$maxDays = date('t', strtotime($request->date));

$start_at = "{$dp[0]}-{$dp[1]}-01";
$end_at = "{$dp[0]}-{$dp[1]}-$maxDays";

$goal = Goal::create(array_merge(
    $request->only(['user_id', 'saldo', 'operation']),
    ['start_at' => $start_at, 'end_at' => $end_at]
));

echo json_encode([
    'data' => $goal,
    'success' => true
]);
    }
}
