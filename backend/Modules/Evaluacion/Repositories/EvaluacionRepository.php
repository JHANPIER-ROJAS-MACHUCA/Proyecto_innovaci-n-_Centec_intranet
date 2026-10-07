<?php
// Puerto de C:\xampp\htdocs\CENTECPC\app\Modules\Evaluacion\models\Evaluacion.php
// Adaptado a: global $capsule + Eloquent (\App\Models\...) del backend nuevo.
// Sin $_SESSION, sin ROOT_PATH, sin DatabaseEval: todo via $capsule / modelos.

class EvaluacionRepository
{
    // Scope de sucursal fiel al origen: sucursal directa del cliente o, si no
    // tiene, la primera sucursal activa asignada a su asesor (usuario_sucursal).
    private static function sucScope(): string
    {
        return 'COALESCE((SELECT s.idS FROM sucursales s WHERE s.idS = c.idSucursal), (SELECT MIN(us.idS) FROM usuario_sucursal us WHERE us.idU = c.idU AND us.estado = 1 AND (us.fecha_fin IS NULL OR us.fecha_fin >= CURRENT_DATE)))';
    }
    // ── Cliente ──
    public static function getClienteById(int $id): ?array
    {
        global $capsule;
        $row = $capsule->table('tclie_general')->where('idCG', $id)->first();
        return $row ? (array) $row : null;
    }

    public static function getClienteByDni(string $dni): ?array
    {
        global $capsule;
        $row = $capsule->table('tclie_general')->where('dni', $dni)->first();
        return $row ? (array) $row : null;
    }

    public static function buscarClientes(string $term, ?int $idSucursal = null): array
    {
        global $capsule;
        $q = $capsule->table('tclie_general as c')
            ->select('c.idCG', 'c.dni', 'c.nom', 'c.ap', 'c.am', 'c.cel', 'c.direc', 'c.distrito');
        if (trim($term) !== '') {
            $t = '%' . trim($term) . '%';
            $q->where(function ($w) use ($t) {
                $w->where('c.dni', 'like', $t)->orWhere('c.nom', 'like', $t)
                  ->orWhere('c.ap', 'like', $t)->orWhere('c.am', 'like', $t);
            });
        }
        if ($idSucursal !== null) {
            $q->whereRaw(self::sucScope() . ' = ?', [$idSucursal]);
        }
        return $q->limit(20)->get()->map(fn($r) => (array) $r)->all();
    }

    // ── Evaluación cabecera (grupo) ──
    public static function getEvaluacion($grupo): ?array
    {
        $c = \App\Models\EvalCredito::where('grupo', $grupo)->first();
        $i = \App\Models\EvalIndicador::where('grupo', $grupo)->first();
        if (!$c && !$i) return null;
        if ($i && !empty($i->eliminado)) return null;
        $row = array_merge($c ? $c->toArray() : [], $i ? $i->toArray() : []);
        if (!empty($row['fecha'] ?? null) && empty($row['fecha_evaluacion'])) {
            $row['fecha_evaluacion'] = $row['fecha'];
        }
        if (!empty($row['id_cliente'])) {
            $cli = self::getClienteById((int) $row['id_cliente']);
            if ($cli) $row = array_merge($row, $cli);
        }
        $row['id_evaluacion'] = $grupo;
        return $row;
    }

    public static function crear(array $data)
    {
        $grupo = (int) (microtime(true) * 1000);
        \App\Models\EvalCredito::create([
            'id_cliente' => $data['id_cliente'], 'id_usuario' => $data['id_usuario'],
            'grupo' => $grupo, 'fecha' => $data['fecha_evaluacion'],
            'monto_solicitado' => $data['monto_solicitado'] ?? 0, 'tem' => $data['tem'] ?? 0,
            'tipo_credito' => $data['tipo_credito'] ?? null,
            'frecuencia_pago' => $data['frecuencia_pago'] ?? null,
            'numero_cuotas' => $data['numero_cuotas'] ?? 0,
            'cuota_estimada' => $data['cuota_estimada'] ?? 0,
            'periodo_cuotas' => $data['periodo_cuotas'] ?? null,
            'condicion' => $data['condicion'] ?? null,
            'monto_propuesto' => $data['monto_propuesto'] ?? 0,
            'fecha_caducidad' => $data['fecha_caducidad'] ?? null,
        ]);
        \App\Models\EvalIndicador::create([
            'id_cliente' => $data['id_cliente'], 'id_usuario' => $data['id_usuario'],
            'grupo' => $grupo, 'fecha' => $data['fecha_evaluacion'],
            'excedente' => $data['excedente'] ?? 0, 'capacidad_pago' => $data['capacidad_pago'] ?? 0,
            'endeudamiento_patrimonial' => $data['endeudamiento_patrimonial'] ?? 0,
            'capital_trabajo' => $data['capital_trabajo'] ?? 0,
            'resultado' => $data['resultado'] ?? null,
            'califica_credito' => $data['califica_credito'] ?? null,
            'monto_atender' => $data['monto_atender'] ?? 0,
            'mora_diaria' => $data['mora_diaria'] ?? 0,
            'ingresos_simple' => $data['ingresos_simple'] ?? null,
            'egresos_simple' => $data['egresos_simple'] ?? null,
            'estado' => !empty($data['finalizar']) ? 'completado' : 'borrador',
        ]);
        return $grupo;
    }

    public static function actualizar($grupo, array $data): void
    {
        $credCols = ['monto_solicitado','tem','tipo_credito','frecuencia_pago','numero_cuotas','cuota_estimada','periodo_cuotas','condicion','monto_propuesto','fecha_caducidad'];
        $indCols = ['excedente','capacidad_pago','endeudamiento_patrimonial','capital_trabajo','resultado','califica_credito','monto_atender','mora_diaria','ingresos_simple','egresos_simple','estado','editar_permiso','editado_una_vez'];
        $c = array_intersect_key($data, array_flip($credCols));
        $i = array_intersect_key($data, array_flip($indCols));
        if ($c) \App\Models\EvalCredito::where('grupo', $grupo)->update($c);
        if ($i) \App\Models\EvalIndicador::where('grupo', $grupo)->update($i);
    }

    public static function finalizar($grupo): void
    {
        \App\Models\EvalIndicador::where('grupo', $grupo)->update(['estado' => 'completado', 'editar_permiso' => 0]);
        self::notificarClienteResultado($grupo);
    }

    private static function notificarClienteResultado($grupo): void
    {
        try {
            global $capsule;
            $c = $capsule->table('eval_credito')->where('grupo', $grupo)->first();
            $i = $capsule->table('eval_indicadores')->where('grupo', $grupo)->first();
            if (!$c || !$i) return;
            if (!in_array($i->resultado ?? '', ['rechazado','riesgo_medio','no_aprobado','no aprobado'], true)) return;
            $capsule->table('notificaciones_cliente')->insert([
                'idCG' => (int) $c->id_cliente, 'titulo' => 'Evaluación no aprobada',
                'mensaje' => 'Tu evaluación crediticia #' . $grupo . ' no ha sido aprobada.',
                'tipo' => 'evaluacion_rechazada',
            ]);
        } catch (\Throwable $e) {}
    }

    public static function permitirEditar($grupo): void
    {
        \App\Models\EvalIndicador::where('grupo', $grupo)->update(['editar_permiso' => 1, 'editado_una_vez' => 0]);
    }

    public static function bloquear($grupo): void
    {
        \App\Models\EvalIndicador::where('grupo', $grupo)->update(['estado' => 'completado', 'editar_permiso' => 0]);
    }

    public static function checkPuedeEditar($grupo, int $idUsuario, $rol): bool
    {
        if ((int) $rol === 5 || (int) $rol === 1) return true;
        global $capsule;
        $i = $capsule->table('eval_indicadores as i')
            ->leftJoin('tclie_general as c', 'i.id_cliente', 'c.idCG')
            ->where('i.grupo', $grupo)->select('i.estado', 'i.editar_permiso', 'i.id_usuario', 'i.id_cliente', 'c.idU', 'i.eliminado')->first();
        if (!$i) return false;
        if (!empty($i->eliminado)) return false;
        $esCreador = (int) $i->id_usuario === $idUsuario;
        $esDueno = (int) ($i->idU ?? 0) === $idUsuario;
        if (!$esCreador && !$esDueno) return false;
        if (($i->estado ?? '') === 'borrador') return true;
        return ($i->estado ?? '') === 'completado' && (int) ($i->editar_permiso ?? 0) === 1;
    }

    public static function softEliminar($grupo, int $idUsuario): void
    {
        global $capsule;
        $capsule->table('eval_indicadores')->where('grupo', $grupo)->update(['eliminado' => 1, 'eliminado_por' => $idUsuario, 'eliminado_at' => date('Y-m-d H:i:s')]);
    }

    public static function eliminarDefinitivo($grupo): void
    {
        foreach (['eval_activos','eval_actividades','eval_costos','eval_deudas','eval_gastos','eval_ingresos','eval_indicadores','eval_credito'] as $t) {
            \App\Models\EvalCredito::resolveConnection()->table($t)->where('grupo', $grupo)->delete();
        }
    }

    // ── Listados ──
    public static function listarEvaluados(?int $idSucursal, string $filtro = '', string $resultado = ''): array
    {
        global $capsule;
        $q = $capsule->table('tclie_general as c')
            ->leftJoin('eval_indicadores as iu', function ($j) { $j->on('iu.id_cliente', 'c.idCG')->whereNull('iu.eliminado')->orWhere('iu.eliminado', 0); })
            ->leftJoin('sucursales as s', 's.idS', 'c.idSucursal')
            ->select('c.idCG as id_cliente', 'c.dni', 'c.nom', 'c.ap', 'c.am', 'c.cel')
            ->selectRaw("COALESCE(s.nombre, 'Sin sucursal') as sucursal_nombre")
            ->selectRaw('COUNT(DISTINCT iu.grupo) as n_eval, MAX(iu.fecha) as ultima_fecha')
            ->groupBy('c.idCG', 'c.dni', 'c.nom', 'c.ap', 'c.am', 'c.cel', 's.nombre');
        if ($idSucursal !== null) $q->whereRaw(self::sucScope() . ' = ?', [$idSucursal]);
        if (trim($filtro) !== '') {
            $t = '%' . trim($filtro) . '%';
            $q->where(function ($w) use ($t) { $w->where('c.dni', 'like', $t)->orWhere('c.nom', 'like', $t)->orWhere('c.ap', 'like', $t); });
        }
        if (trim($resultado) !== '') $q->whereExists(function ($w) use ($resultado) {
            $w->selectRaw('1')->from('eval_indicadores as iu2')->whereColumn('iu2.id_cliente', 'c.idCG')->where('iu2.resultado', $resultado);
        });
        return $q->orderBy('n_eval')->limit(500)->get()->map(fn($r) => (array) $r)->all();
    }

    public static function clientesSinEvaluacion(?int $idSucursal, string $filtro = ''): array
    {
        global $capsule;
        $q = $capsule->table('tclie_general as c')
            ->leftJoin('sucursales as s', 's.idS', 'c.idSucursal')
            ->select('c.idCG as id_cliente', 'c.dni', 'c.nom', 'c.ap', 'c.am', 'c.cel', 'c.rubro')
            ->selectRaw("COALESCE(s.nombre, 'Sin sucursal') as sucursal_nombre")
            ->whereNotExists(fn($w) => $w->selectRaw('1')->from('eval_indicadores as i')->whereColumn('i.id_cliente', 'c.idCG')->where('i.eliminado', 0));
        if ($idSucursal !== null) $q->whereRaw(self::sucScope() . ' = ?', [$idSucursal]);
        if (trim($filtro) !== '') {
            $t = '%' . trim($filtro) . '%';
            $q->where(function ($w) use ($t) { $w->where('c.dni', 'like', $t)->orWhere('c.nom', 'like', $t)->orWhere('c.ap', 'like', $t); });
        }
        return $q->orderByDesc('c.idCG')->limit(500)->get()->map(fn($r) => (array) $r)->all();
    }

    public static function proximosVencer(?int $idSucursal, string $filtro = ''): array
    {
        global $capsule;
        $q = $capsule->table('tclie_general as c')
            ->join('eval_indicadores as iu', 'iu.id_cliente', 'c.idCG')
            ->leftJoin('eval_credito as cr', 'iu.grupo', 'cr.grupo')
            ->leftJoin('sucursales as s', 's.idS', 'c.idSucursal')
            ->select('c.idCG as id_cliente', 'c.dni', 'c.nom', 'c.ap', 'c.am', 'c.cel', 'iu.grupo', 'iu.grupo as id_evaluacion', 'iu.fecha as fecha_evaluacion')
            ->selectRaw("COALESCE(s.nombre, 'Sin sucursal') as sucursal_nombre")
            ->addSelect('iu.resultado', 'iu.estado', 'cr.monto_solicitado', 'cr.frecuencia_pago', 'cr.numero_cuotas', 'cr.fecha_caducidad')
            ->where('iu.eliminado', 0)->whereNotNull('cr.fecha_caducidad')
            ->whereRaw('cr.fecha_caducidad BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 3 MONTH)');
        if ($idSucursal !== null) $q->whereRaw(self::sucScope() . ' = ?', [$idSucursal]);
        if (trim($filtro) !== '') {
            $t = '%' . trim($filtro) . '%';
            $q->where(function ($w) use ($t) { $w->where('c.dni', 'like', $t)->orWhere('c.nom', 'like', $t)->orWhere('c.ap', 'like', $t); });
        }
        return $q->orderBy('cr.fecha_caducidad')->limit(500)->get()->map(fn($r) => (array) $r)->all();
    }

    public static function getEvaluacionesByCliente(int $idCG): array
    {
        global $capsule;
        return $capsule->table('eval_indicadores as i')->leftJoin('eval_credito as cr', 'i.grupo', 'cr.grupo')
            ->where('i.id_cliente', $idCG)->where('i.eliminado', 0)->orderByDesc('i.fecha')
            ->select('i.id_indicador', 'i.grupo', 'i.id_cliente', 'i.fecha as fecha_evaluacion', 'i.resultado', 'i.estado', 'i.editar_permiso', 'i.monto_atender', 'cr.monto_solicitado', 'cr.cuota_estimada', 'cr.condicion', 'cr.monto_propuesto', 'cr.frecuencia_pago', 'cr.numero_cuotas', 'cr.fecha_caducidad')
            ->get()->map(fn($r) => (array) $r)->all();
    }

    // ── Detalles ──
    public static function getDetalles($grupo): array
    {
        global $capsule;
        $out = [];
        foreach (['actividades' => 'eval_actividades', 'costos' => 'eval_costos', 'deudas' => 'eval_deudas', 'gastos' => 'eval_gastos', 'ingresos' => 'eval_ingresos', 'activos_detalle' => 'eval_activos'] as $k => $t) {
            $out[$k] = $capsule->table($t)->where('grupo', $grupo)->get()->map(fn($r) => (array) $r)->all();
        }
        return $out;
    }

    public static function guardarDetalles($grupo, string $tabla, array $items): void
    {
        global $capsule;
        $capsule->table($tabla)->where('grupo', $grupo)->delete();
        if (!$items) return;
        $base = $capsule->table('eval_credito')->where('grupo', $grupo)->first();
        if (!$base) return;
        foreach ($items as $row) {
            $capsule->table($tabla)->insert(array_merge([
                'id_cliente' => $base->id_cliente, 'id_usuario' => $base->id_usuario,
                'grupo' => $grupo, 'fecha' => $base->fecha,
            ], $row));
        }
    }

    public static function getConyugeByCliente(int $idCG): ?array
    {
        global $capsule;
        $row = $capsule->table('tvinculacion as v')->join('tclie_general as c2', 'c2.idCG', 'v.conyugue')
            ->where('v.titular', $idCG)->select('c2.idCG', 'c2.dni', 'c2.nom', 'c2.ap', 'c2.am')->first();
        return $row ? (array) $row : null;
    }

    public static function statsGlobal(?int $idSucursal): array
    {
        global $capsule;
        $c = $capsule->table('tclie_general as c');
        if ($idSucursal !== null) $c->whereRaw(self::sucScope() . ' = ?', [$idSucursal]);
        $total = (clone $c)->count();
        $evaluados = $capsule->table('eval_indicadores as i')->join('tclie_general as c', 'i.id_cliente', 'c.idCG')
            ->where('i.eliminado', 0)->when($idSucursal !== null, fn($q) => $q->whereRaw(self::sucScope() . ' = ?', [$idSucursal]))->distinct()->count('i.id_cliente');
        return ['total' => $total, 'evaluados' => $evaluados, 'sin_evaluar' => max(0, $total - $evaluados)];
    }
}
