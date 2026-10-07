<?php
// Puerto de:
// - C:\xampp\htdocs\CENTECPC\app\Modules\Sucursales\Models\SucursalesModel.php
// - C:\xampp\htdocs\CENTECPC\app\Modules\Sucursales\Models\PagoCliente.php (lecturas)
// - C:\xampp\htdocs\CENTECPC\app\Modules\Admin\models\Sucursal.php::getAsesoresDeSucursal
// Nota: registrarPago() NO se duplica: ya existe como ClienteController::pagarDeuda.
// Adaptado a global $capsule + Eloquent del backend nuevo.

class SucursalRepository
{
    public static function principal(): ?array
    {
        $r = \App\Models\Sucursal::where('tipo', 'principal')->where('estado', 1)->first();
        return $r ? $r->toArray() : null;
    }

    public static function todas(): array
    {
        return \App\Models\Sucursal::where('estado', 1)->orderByRaw("tipo DESC, nombre ASC")
            ->get()->map(fn($r) => $r->toArray())->all();
    }

    public static function porId(int $idS): ?array
    {
        $r = \App\Models\Sucursal::where('idS', $idS)->where('estado', 1)->first();
        return $r ? $r->toArray() : null;
    }

    public static function empresa(int $empresaId): ?array
    {
        $r = \App\Models\Empresa::where('id', $empresaId)->where('estado', 1)->first();
        return $r ? $r->toArray() : null;
    }

    // Métodos de pago ACTIVOS de la sucursal (tabla intermedia, orden de la sucursal)
    public static function metodosPago(int $idS): array
    {
        global $capsule;
        return $capsule->table('bank_accounts as b')
            ->join('sucursal_metodo_pago as sm', 'sm.banco_id', 'b.id')
            ->where('sm.idS', $idS)->where('sm.estado', 1)->where('b.estado', 1)
            ->select('b.*', 'sm.estado as estado', 'sm.orden as orden')
            ->orderBy('sm.orden')->orderBy('b.id')
            ->get()->map(fn($r) => (array) $r)->all();
    }

    // Adjunta empresa + métodos de pago (origen: SucursalesModel::conMetodosPago)
    public static function conMetodosPago(array &$suc): void
    {
        if (!$suc) return;
        $suc['empresa'] = self::empresa((int) ($suc['empresa_id'] ?? 0));
        $suc['metodos_pago'] = !empty($suc['idS']) ? self::metodosPago((int) $suc['idS']) : [];
    }

    // Tipos por sucursal (slugs JSON en la fila sucursales)
    private static function tiposPorSucursal(int $idS, string $col, string $tabla): array
    {
        global $capsule;
        $row = $capsule->table('sucursales')->where('idS', $idS)->select($col)->first();
        $slugs = $row && !empty($row->$col) ? json_decode($row->$col, true) : [];
        if (!$slugs) return [];
        return $capsule->table($tabla)->whereIn('slug', $slugs)->where('estado', 1)
            ->orderBy('orden')->get()->map(fn($r) => (array) $r)->all();
    }

    public static function tiposCredito(int $idS): array
    {
        return self::tiposPorSucursal($idS, 'tipos_credito', 'tipos_credito');
    }

    public static function tiposAhorro(int $idS): array
    {
        return self::tiposPorSucursal($idS, 'tipos_ahorro', 'tipos_ahorro');
    }

    public static function tiposSeguro(int $idS): array
    {
        return self::tiposPorSucursal($idS, 'tipos_seguro', 'tipos_seguro');
    }

    public static function servicios(int $idS): array
    {
        global $capsule;
        $row = $capsule->table('sucursales')->where('idS', $idS)->select('servicios')->first();
        $srv = $row && !empty($row->servicios) ? json_decode($row->servicios, true) : [];
        if (!is_array($srv)) return [];
        return array_values(array_filter(array_map('trim', $srv), fn($s) => $s !== ''));
    }

    // Asesores de la sucursal. Origen fiel Admin/Sucursal.php:229
    // (tusuarios + tdatosu + rol, relación usuario_sucursal).
    public static function asesores(int $idS): array
    {
        global $capsule;
        return $capsule->table('usuario_sucursal as us')
            ->join('tusuarios as u', 'u.idU', 'us.idU')
            ->join('tdatosu as d', 'd.idU', 'u.idU')
            ->leftJoin('rol as r', 'r.id', 'u.idRol')
            ->where('us.idS', $idS)
            ->select('u.idU', 'u.idEstado', 'd.nomU', 'd.apU', 'd.amU', 'd.dniU', 'd.celU', 'd.correoU', 'd.fotoU', 'us.fecha_inicio', 'us.fecha_fin', 'r.tipo as rolNombre')
            ->orderByRaw('(u.idEstado = 1) DESC, d.nomU ASC')
            ->get()->map(fn($r) => (array) $r)->all();
    }

    public static function actualizarHorarioPago(int $idS, string $texto): void
    {
        \App\Models\Sucursal::where('idS', $idS)->update(['horario_pago' => mb_substr(trim($texto), 0, 500)]);
    }

    // ── Contexto de pago del cliente (origen PagoCliente, solo lectura) ──
    public static function deudasCliente(int $idCG): array
    {
        global $capsule;
        $prestamos = $capsule->table('tprestamo')->where('idCG', $idCG)->orderByDesc('idP')->get();
        $cli = $capsule->table('tclie_general')->where('idCG', $idCG)->select('idCG', 'idSucursal', 'codigo_cliente')->first();
        $out = [];
        foreach ($prestamos as $p) {
            $p = (array) $p;
            $pend = $capsule->table('tpresta_detalle')->where('idP', $p['idP'])->where('is_finished', 0)
                ->selectRaw('COALESCE(SUM(COALESCE(cuota, capital + interest, 0)),0) - COALESCE(SUM(COALESCE(montoPagado, capital_payment + interest_payment, 0)),0) as pendiente')->first();
            $out[] = [
                'idP' => $p['idP'], 'capital' => $p['capital'] ?? 0,
                'saldo_pendiente' => (float) ($pend->pendiente ?? 0),
                'documento_deuda' => $p['n_credito'] ?? ($cli->codigo_cliente ?? ''),
                'fecha_deuda' => $p['fechaDesembolso'] ?? null,
                'estado' => $p['estado'], 'finished_at' => $p['finished_at'] ?? null,
            ];
        }
        return $out;
    }

    public static function contextoPago(int $idCG, ?int $receptorSucId = null): array
    {
        global $capsule;
        $cli = $capsule->table('tclie_general')->where('idCG', $idCG)->select('idSucursal', 'idU')->first();
        $idSucCliente = (int) ($cli->idSucursal ?? 0);
        $sucCliente = $idSucCliente
            ? $capsule->table('sucursales as s')->leftJoin('empresas as e', 's.empresa_id', 'e.id')->where('s.idS', $idSucCliente)->select('s.*', 'e.nombre as empresa_nombre')->first()
            : null;
        if (!$sucCliente) {
            $sucCliente = $capsule->table('sucursales as s')->leftJoin('empresas as e', 's.empresa_id', 'e.id')->where('s.tipo', 'principal')->where('s.estado', 1)->select('s.*', 'e.nombre as empresa_nombre')->first();
        }
        $receptor = ($receptorSucId > 0)
            ? $capsule->table('sucursales as s')->leftJoin('empresas as e', 's.empresa_id', 'e.id')->where('s.idS', $receptorSucId)->where('s.estado', 1)->select('s.*', 'e.nombre as empresa_nombre')->first()
            : null;
        if (!$receptor) $receptor = $sucCliente;
        $sc = (array) $sucCliente; $rc = (array) $receptor;
        return [
            'cliente_sucursal_nombre' => $sc['nombre'] ?? '',
            'cliente_empresa_nombre' => $sc['empresa_nombre'] ?? '',
            'receptor_sucursal_nombre' => $rc['nombre'] ?? ($sc['nombre'] ?? ''),
            'receptor_sucursal_id' => (int) ($rc['idS'] ?? 0),
            'metodos_pago' => !empty($rc['idS']) ? self::metodosPago((int) $rc['idS']) : [],
        ];
    }

    public static function comprobantes(int $idCG, int $limite = 20): array
    {
        global $capsule;
        return $capsule->table('pago_comprobantes as pc')
            ->leftJoin('bank_accounts as b', 'b.id', 'pc.banco_id')
            ->leftJoin('sucursales as s', 's.idS', 'pc.sucursal_id')
            ->where('pc.idCG', $idCG)
            ->select('pc.*', 'b.name as banco_nombre', 'b.number as banco_numero', 's.nombre as receptor_sucursal')
            ->orderByDesc('pc.creado_en')->limit($limite)
            ->get()->map(fn($r) => (array) $r)->all();
    }
}
