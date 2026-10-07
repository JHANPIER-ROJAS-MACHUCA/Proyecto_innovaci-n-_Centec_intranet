<?php
// Puerto de C:\xampp\htdocs\CENTECPC\app\Modules\Cliente\models\Cliente.php
// Registro completo: tclie_general + tclie_direccion + tclie_negocio +
// cónyuge/avales en tclie_general + tvinculacion + código por sucursal (STP-0001).

class ClienteRegistroRepository
{
    public static function generarCodigoCliente(int $idSucursal): string
    {
        global $capsule;
        $s = $capsule->table('sucursales')->where('idS', $idSucursal)->first();
        $prefijo = strtoupper(trim($s->codigo ?? ''));
        if ($prefijo === '') $prefijo = 'SUC';
        $row = $capsule->table('tclie_general')->where('idSucursal', $idSucursal)
            ->selectRaw("COALESCE(MAX(CAST(SUBSTRING_INDEX(codigo_cliente, '-', -1) AS UNSIGNED)), 0) + 1 AS next")->first();
        return $prefijo . '-' . str_pad((string) ((int) ($row->next ?? 1)), 4, '0', STR_PAD_LEFT);
    }

    public static function crearCompleto(array $d): int
    {
        global $capsule;
        $conn = $capsule->getConnection();
        $conn->beginTransaction();
        try {
            $nextId = (int) $capsule->table('tclie_general')->max('idCG') + 1;
            $capsule->table('tclie_general')->insert([
                'idCG' => $nextId, 'dni' => $d['dni'] ?? '', 'nom' => $d['nom'] ?? '',
                'ap' => $d['ap'] ?? '', 'am' => $d['am'] ?? '', 'direc' => $d['direc'] ?? '',
                'correo' => $d['correo'] ?? '', 'telefono' => $d['telefono'] ?? '', 'cel' => $d['cel'] ?? '',
                'estado_civil' => $d['estado_civil'] ?? '', 'tipo' => $d['tipo'] ?? '',
                'tipo_cliente' => $d['tipo_cliente'] ?? null, 'status' => 'ACTIVE',
                'idU' => $d['idU'] ?? null, 'idSucursal' => $d['idSucursal'] ?? null,
                'codigo_cliente' => !empty($d['idSucursal']) ? self::generarCodigoCliente((int) $d['idSucursal']) : null,
                'sexo' => $d['sexo'] ?? '', 'fec_nac' => $d['fec_nac'] ?? null,
                'lugar_nac' => $d['lugar_nac'] ?? '', 'grado_inst' => $d['grado_inst'] ?? '',
                'n_hijos' => $d['n_hijos'] ?? '0', 'rubro' => $d['rubro'] ?? '',
                'referencia' => $d['referencia'] ?? '', 'distrito' => $d['distrito'] ?? '',
                'provincia' => $d['provincia'] ?? '', 'departamento' => $d['departamento'] ?? '',
                'tiempo_residencia' => $d['tiempo_residencia'] ?? '', 'fecha_registro' => date('Y-m-d H:i:s'),
                'coordinate_lat' => $d['coordinate_lat'] ?? '', 'coordinate_lng' => $d['coordinate_lng'] ?? '',
                'ubigeo_id' => $d['ubigeo_id'] ?? null,
                'tipo_persona' => $d['tipo_persona'] ?? '', 'ruc' => $d['ruc'] ?? '',
                'razon_social' => $d['razon_social'] ?? '',
            ]);
            if (!empty($d['direccion_data'])) self::insertDireccion($nextId, $d['direccion_data']);
            if (!empty($d['negocio_data'])) self::insertNegocio($nextId, $d['negocio_data']);
            if (!empty($d['conyuge'])) {
                $cid = self::insertRelated($d['conyuge'], $d['idU'] ?? null);
                self::insertVinculacion($nextId, 'conyugue', $cid);
            }
            foreach (($d['avales'] ?? []) as $aval) {
                self::insertVinculacion($nextId, 'aval', self::insertRelated($aval, $d['idU'] ?? null));
            }
            $conn->commit();
            return $nextId;
        } catch (\Throwable $e) {
            $conn->rollBack();
            throw $e;
        }
    }

    private static function insertDireccion(int $idCG, array $dir): void
    {
        global $capsule;
        $capsule->table('tclie_direccion')->insert([
            'idCD' => (int) $capsule->table('tclie_direccion')->max('idCD') + 1,
            'idCG' => $idCG, 'iddis' => $dir['iddis'] ?? null,
            'anexo' => $dir['anexo'] ?? '', 'direc' => $dir['direc'] ?? '', 'referencia' => $dir['referencia'] ?? '',
        ]);
    }

    private static function insertNegocio(int $idCG, array $neg): void
    {
        global $capsule;
        $capsule->table('tclie_negocio')->insert([
            'idCN' => (int) $capsule->table('tclie_negocio')->max('idCN') + 1,
            'idCG' => $idCG, 'direccion' => $neg['direccion'] ?? '', 'tipo' => $neg['tipo'] ?? '',
            'tipoLocal' => $neg['tipoLocal'] ?? '', 'tipoNegocio' => $neg['tipoNegocio'] ?? '', 'tiempo' => $neg['tiempo'] ?? '',
        ]);
    }

    private static function insertRelated(array $p, $idU): int
    {
        global $capsule;
        $id = (int) $capsule->table('tclie_general')->max('idCG') + 1;
        $capsule->table('tclie_general')->insert([
            'idCG' => $id, 'dni' => $p['dni'] ?? '', 'nom' => $p['nom'] ?? '', 'ap' => $p['ap'] ?? '',
            'am' => $p['am'] ?? '', 'direc' => $p['direc'] ?? '', 'cel' => $p['cel'] ?? '',
            'estado_civil' => $p['estado_civil'] ?? '', 'status' => 'ACTIVE', 'idU' => $idU,
            'fecha_registro' => date('Y-m-d H:i:s'),
        ]);
        return $id;
    }

    private static function insertVinculacion(int $titular, string $tipo, int $relacionado): void
    {
        global $capsule;
        $col = $tipo === 'conyugue' ? 'conyugue' : 'aval';
        $capsule->table('tvinculacion')->insert([
            'idV' => (int) $capsule->table('tvinculacion')->max('idV') + 1,
            'titular' => $titular, $col => $relacionado,
        ]);
    }

    public static function fichaCompleta(int $idCG): array
    {
        global $capsule;
        $c = $capsule->table('tclie_general as c')->leftJoin('sucursales as s', 's.idS', 'c.idSucursal')
            ->where('c.idCG', $idCG)->select('c.*', 's.nombre as sucursal_nombre')->first();
        if (!$c) throw new DomainException('Cliente no existe.');
        $out = (array) $c;
        $out['direcciones'] = $capsule->table('tclie_direccion')->where('idCG', $idCG)->get()->map(fn($r) => (array) $r)->all();
        $out['negocios'] = $capsule->table('tclie_negocio')->where('idCG', $idCG)->get()->map(fn($r) => (array) $r)->all();
        $out['vinculaciones'] = $capsule->table('tvinculacion')->where('titular', $idCG)->get()->map(fn($r) => (array) $r)->all();
        return $out;
    }
}
