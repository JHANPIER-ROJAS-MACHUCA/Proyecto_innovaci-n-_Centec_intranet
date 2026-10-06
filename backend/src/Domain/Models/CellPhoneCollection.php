<?php

namespace CrediSoporte\Domain\Models;

use Illuminate\Database\Eloquent\Model;
use CrediSoporte\Domain\Models\Credit;
use CrediSoporte\Domain\Request\Request;
use DateTime;

class CellPhoneCollection extends Model
{
    public function find()
    {
        $request = new Request();

        $credits = Credit::with('installments')
            ->select(
                'tprestamo.idP',
                'tprestamo.n_credito',
                'tprestamo.montoAprovado',
                'tprestamo.mora',
                'tprestamo.tipoP',
                'tclie_general.ap',
                'tclie_general.am',
                'tclie_general.nom',
                'tclie_general.cel',
            )
            ->selectRaw('concat_ws(" ",tclie_general.ap, tclie_general.am, tclie_general.nom) as customer')
            ->join('tclie_general', 'tprestamo.idCG', 'tclie_general.idCG')
            ->where(function ($query) use ($request) {
                $query->whereRaw("concat_ws(' ', tclie_general.ap, tclie_general.am, tclie_general.nom) like '%$request->search%'")
                    ->orWhere('tclie_general.dni', 'like', "'$request->search%'");
            })
            ->where('tprestamo.estado', 4)
            ->limit(20)
            ->get();

        $creditos = collect();
        foreach ($credits as $credit) {

            $cuotas = collect();
            $totalPendienteCuota = 0; // pendiente capital + interes
            $totalCuota = 0;
            $totalMora = 0;
            foreach ($credit->installments as $key => $installment) {
                $pendiente = $installment->debtInstallment();

                $nextInstallment = $credit->installments[$key + 1] ?? null;
                $mora = $installment->debtMora($credit->mora, $nextInstallment);

                $diasAtrasados = 0;
                if ($installment->estado === '1') {
                    $ini = new DateTime($installment->fechaProg);
                    $fin = new DateTime($installment->fechaPago);
                    $diasAtrasados = $ini->diff($fin)->days;
                } else {
                    $ini = new DateTime($installment->fechaProg);
                    $fin = new DateTime(date('Y-m-d'));

                    if ($installment->fechaProg < date('Y-m-d')) {
                        $diasAtrasados = $ini->diff($fin)->days * -1;
                    } else {
                        $diasAtrasados = $ini->diff($fin)->days;
                    }
                }

                $totalCuota += $pendiente;

                if ($installment->fechaProg <= date('Y-m-d')) {
                    $totalPendienteCuota += $pendiente;
                }

                if ($pendiente > 0 || $mora > 0) {
                    $totalMora += $mora;

                    $cuotas->push([
                        'id' => $installment->idPD,
                        'number' => $installment->ncuota,
                        'expiration_at' => $installment->fechaProg,
                        'amount' => $installment->amount(),
                        'slope' => $pendiente,
                        'mora' => $mora,
                        'days' => $diasAtrasados,
                    ]);
                }
            }

            $lastInstallment = $credit->installments[count($credit->installments) - 1];
            $otras_moras = $credit->debtMora($lastInstallment);

            $totalMora += $otras_moras;

            $creditos->push([
                'id' => $credit->idP,
                'customer' => $credit->customer,
                'number' => $credit->n_cuota,
                'cell_phone' => $credit->cel,
                'installments' => $cuotas,
                'pendiente' => round($totalPendienteCuota, 1),
                'total_mora' => round($totalMora, 1),
                'total_installment' => round($totalCuota, 1),
                'product' => $credit->productTypeToString()
            ]);
        }

        return $creditos;
    }
}
