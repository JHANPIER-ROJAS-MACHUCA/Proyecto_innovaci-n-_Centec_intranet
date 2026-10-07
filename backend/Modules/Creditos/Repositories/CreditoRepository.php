<?php
class CreditoRepository
{
    public static function createVinculacion(?int $titular, $conyugeId, $avalId): int
    {
        global $capsule;
        return $capsule->table('tvinculacion')->insertGetId([
            'titular'  => $titular,
            'conyugue' => $conyugeId,
            'aval'     => $avalId,
        ]);
    }

    public static function create(array $data)
    {
        return \App\Models\Credit::create($data);
    }

    public static function find(int $id): ?object
    {
        return \App\Models\Credit::where('id', $id)->first();
    }

    public static function byCustomer(int $idCG)
    {
        return \App\Models\Credit::where('idCG', $idCG)->get();
    }

    public static function byEstado(int|array $estado, int $limit = 100)
    {
        return \App\Models\Credit::whereIn('estado', (array) $estado)->orderBy('idP', 'desc')->limit($limit)->get();
    }

    public static function reemplazarCuotas(object $credit, array $installments): void
    {
        global $capsule;
        $conn = $capsule->getConnection();
        $conn->beginTransaction();
        try {
            \App\Models\Installment::where('idP', $credit->idP)->delete();
            $credit->save();
            foreach ($installments as $installment) $installment->save();
            \App\Models\Transaction::where('tipo', 2)->where('conejo', $credit->idP)->update(['total' => $credit->montoAprovado]);
            $conn->commit();
        } catch (\Throwable $th) {
            $conn->rollBack();
            throw $th;
        }
    }

    public static function tipos()
    {
        global $capsule;
        return $capsule->table('credit_types')->get();
    }
}
