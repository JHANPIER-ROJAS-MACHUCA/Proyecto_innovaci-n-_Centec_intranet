<?php

namespace CrediSoporte\Modules\Credits\Legacy;

use CrediSoporte\Domain\Models\Credit;
use CrediSoporte\Domain\Request\Request;

// Logica migrada verbatim desde app/apiMobile/listaCobrosHoy.php.
// El wrapper en app/apiMobile/listaCobrosHoy.php preserva URL, entradas y salida legacy.
class ListaCobrosHoy
{
    public static function handle(): void
    {$request = new Request();

if (!$request->user()) {
    echo json_encode([]);
    die();
}

$dateNow = date('Y-m-d');

$credits = Credit::with([
    'installments', 'comment' => function ($query) use ($dateNow) {
        $query->join('justify_types', 'non_payment_justifications.category_id', 'justify_types.id')
            ->whereRaw("date(created_at) = '$dateNow'")
            ->orderBy('id', 'desc')
            ->select('non_payment_justifications.*', 'justify_types.description as category');
    }, 'transactions' => function ($query) use ($dateNow) {
        $query->whereRaw("date(created_at) = '$dateNow'")
            ->where('estadodt', '2')
            ->where('tipo', '3')
            ->select('idCAD', 'total', 'conejo');
    }
])
    ->select(
        'tprestamo.idP',
        'tprestamo.number_installments',
        'tprestamo.capital',
        'tprestamo.penalty',
        'tprestamo.tipoP',
        'tprestamo.payment_period',
        'tclie_general.cel as cell_phone',
        'tclie_general.idCG',
        'tclie_general.coordinate_lat',
        'tclie_general.coordinate_lng'
    )
    ->selectRaw('concat_ws(" ",tclie_general.ap, tclie_general.am, tclie_general.nom) as customer')
    ->join('tpresta_detalle', 'tprestamo.idP', 'tpresta_detalle.idP')
    ->join('tclie_general', 'tprestamo.idCG', 'tclie_general.idCG')
    ->where('tprestamo.estado', 4)
    ->when($request->search, function ($query) use ($request) {
        $search = (string) $request->search;
        $query->where(function ($query) use ($search) {
            $query->whereRaw("concat_ws(' ', tclie_general.ap, tclie_general.am, tclie_general.nom) like ?", ['%' . $search . '%'])
                ->orWhere('tclie_general.dni', 'like', $search . '%');
        });
    }, function ($query) use ($request) {
        $query->where('tpresta_detalle.expiration_at', date('Y-m-d'))
            ->where('tclie_general.idU', $request->user()->idU);
    })
    ->groupBy('tprestamo.idP')
    ->orderBy('tclie_general.ap')
    ->get();

$creditosDiferentesDeDiarios = Credit::with([
    'installments', 'comment' => function ($query) use ($dateNow) {
        $query->join('justify_types', 'non_payment_justifications.category_id', 'justify_types.id')
            ->whereRaw("date(created_at) = '$dateNow'")
            ->orderBy('id', 'desc')
            ->select('non_payment_justifications.*', 'justify_types.description as category');
    }, 'transactions' => function ($query) use ($dateNow) {
        $query->whereRaw("date(created_at) = '$dateNow'")
            ->where('estadodt', '2')
            ->where('tipo', '3')
            ->select('idCAD', 'total', 'conejo');
    }
])
    ->join('tpresta_detalle', 'tprestamo.idP', 'tpresta_detalle.idP')
    ->join('tclie_general', 'tprestamo.idCG', 'tclie_general.idCG')
    ->where('tprestamo.estado', '4') // activos a cobros
    ->where('tprestamo.payment_period', '!=', 'daily') // diferente de diario
    ->where('tpresta_detalle.expiration_at', '<', $dateNow)
    ->where('tclie_general.idU', $request->user()->idU)
    ->whereNull('tpresta_detalle.payment_date')
    ->groupBy('tprestamo.idP')
    ->select(
        'tprestamo.idP',
        'tprestamo.n_credito',
        'tprestamo.capital',
        'tprestamo.penalty',
        'tprestamo.tipoP',
        'tclie_general.cel as cell_phone'
    )
    ->selectRaw('concat_ws(" ",tclie_general.ap, tclie_general.am, tclie_general.nom) as customer')
    ->get();

echo json_encode([
    'data' => $credits,
    'charges' => $creditosDiferentesDeDiarios
]);
    }
}
