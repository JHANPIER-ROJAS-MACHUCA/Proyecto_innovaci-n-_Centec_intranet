<?php

namespace CrediSoporte\Modules\Goals\Legacy;

use CrediSoporte\Domain\Models\Goal;
use CrediSoporte\Domain\Models\User;
use CrediSoporte\Domain\Request\Request;

// Logica migrada verbatim desde app/api/updateMeta.php.
// El wrapper en app/api/updateMeta.php preserva URL, entradas y salida legacy.
class UpdateMeta
{
    public static function handle(): void
    {$request = new Request();

$goal = Goal::find($request->id);

$errors = $request->validate([
    'id' => 'required|exists',
    'user_id' => 'required|exists',
    'date' => 'required|date|unique',
    'saldo' => 'required|numeric|min:1',
    'operation' => 'required|numeric|min:1'
], [], [
    'id.exists' => $goal ? true : false,
    'user_id.exists' => User::where('idU', $request->user_id)->exists(),
    'date.unique' => Goal::where('user_id', $request->user_id)
        ->whereRaw("? between start_at and end_at", [$request->date])
        ->where('id', '!=', $request->id)
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
$maxDays = cal_days_in_month(CAL_GREGORIAN, $dp[1], $dp[0]);

$start_at = "{$dp[0]}-{$dp[1]}-01";
$end_at = "{$dp[0]}-{$dp[1]}-$maxDays";

$goal->user_id = $request->user_id;
$goal->saldo = $request->saldo;
$goal->operation = $request->operation;
$goal->start_at = $start_at;
$goal->end_at = $end_at;
$goal->save();

echo json_encode([
    'data' => $goal,
    'success' => true
]);
    }
}
