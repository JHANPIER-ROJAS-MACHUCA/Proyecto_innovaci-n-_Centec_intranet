<?php
// Módulo Creditos — contrato de crédito (extraído de services/PanelService.php,
// phase2-modular; idéntico). Usa RelationRepository + EmpresaService (compartidos).

class FormatoService
{
    const PAGOS = [1 => 'diario', 2 => 'semanal', 3 => 'pago único', 4 => 'mensual', 5 => 'quincenal'];

    public static function contrato(int $idP): array
    {
        $credit = \App\Models\Credit::join('tclie_general', 'tprestamo.idCG', 'tclie_general.idCG')
            ->select('tprestamo.*', 'tclie_general.dni', 'tclie_general.direc', 'tclie_general.cel')
            ->selectRaw('concat_ws(" ", tclie_general.ap, tclie_general.am, tclie_general.nom) as client')
            ->where('tprestamo.idP', $idP)->first();
        if (!$credit) throw new DomainException('Crédito no existe.');
        $meses = ['January' => 'Enero', 'February' => 'Febrero', 'March' => 'Marzo', 'April' => 'Abril', 'May' => 'Mayo', 'June' => 'Junio', 'July' => 'Julio', 'August' => 'Agosto', 'September' => 'Septiembre', 'October' => 'Octubre', 'November' => 'Noviembre', 'December' => 'Diciembre'];
        return [
            'credit' => $credit,
            'vinculacion' => $credit->idV ? RelationRepository::vinculacionCompleta($credit->idV) : null,
            'negocio' => EmpresaService::ver(),
            'numero' => str_pad((string) $idP, 5, '0', STR_PAD_LEFT),
            'fecha' => date('d') . ' días del mes de ' . strtr(date('F'), $meses) . ' de ' . date('Y'),
            'formaPago' => self::PAGOS[(int) $credit->pago] ?? (string) $credit->pago,
        ];
    }
}
