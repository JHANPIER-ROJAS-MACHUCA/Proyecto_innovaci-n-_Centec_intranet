<?php
// Módulo Evaluacion — servicio (movido de services/, phase2-modular; idéntico).
require_once __DIR__ . '/../Repositories/EvaluacionRepository.php';

class EvaluacionService
{
    // ── Fórmulas Excel (functions.php origen) ──
    public static function cuotaExcel(float $monto, float $temDecimal, int $plazo): float
    {
        if ($plazo <= 0) return 0;
        return ceil((($monto + ($monto * $temDecimal)) / $plazo) * 10) / 10;
    }

    public static function capacidadPago(float $cuota, float $excedente): float
    {
        return $excedente <= 0 ? 0 : $cuota / $excedente;
    }

    public static function endeudamiento(float $pasivo, float $patrimonio): float
    {
        return $patrimonio <= 0 ? 0 : $pasivo / $patrimonio;
    }

    public static function capitalTrabajo(float $activoCorriente, float $pasivoCorriente): float
    {
        return $activoCorriente - $pasivoCorriente;
    }

    public static function determinarRiesgo(float $endeud, float $capTrabajo, float $excedente, float $cuota): string
    {
        if ($endeud >= 1.0 || $capTrabajo < 0) return 'rechazado';
        return $excedente >= $cuota ? 'aprobado' : 'rechazado';
    }

    // ── Parseos POST (origen parseActividades/Costos/Deudas/Gastos/Ingresos/Activos) ──
    public static function parseActividades(array $p): array
    {
        $out = [];
        $n = count($p['actividad_desc'] ?? []);
        for ($i = 0; $i < $n; $i++) {
            if (trim($p['actividad_desc'][$i] ?? '') === '') continue;
            $out[] = ['numero' => $i + 1, 'descripcion' => trim($p['actividad_desc'][$i]),
                'venta_baja' => (float) ($p['actividad_vbaja'][$i] ?? 0),
                'venta_alta' => (float) ($p['actividad_valta'][$i] ?? 0),
                'promedio_ponderado' => (float) ($p['actividad_promedio'][$i] ?? 0)];
        }
        return $out;
    }

    public static function parseCostos(array $p): array
    {
        $out = [];
        foreach (($p['costo_mensual'] ?? []) as $i => $mm) {
            if ((float) $mm <= 0) continue;
            $out[] = ['descripcion' => '', 'frecuencia' => 'MENSUAL',
                'monto_mensual' => (float) $mm, 'monto_diario' => (float) ($p['costo_diario'][$i] ?? 0)];
        }
        return $out;
    }

    public static function parseDeudas(array $p): array
    {
        $out = [];
        foreach (($p['df_entidad'] ?? []) as $i => $ent) {
            if (trim($ent) === '') continue;
            $out[] = ['entidad' => trim($ent), 'frecuencia_pago' => $p['df_frec'][$i] ?? 'DIARIO',
                'monto' => (float) ($p['df_monto'][$i] ?? 0), 'periodo' => (int) ($p['df_periodo'][$i] ?? 0),
                'tasa_promedio' => 0, 'condicion' => $p['df_condicion'][$i] ?? 'NORMAL',
                'cuota' => (float) ($p['df_cuota'][$i] ?? 0)];
        }
        return $out;
    }

    public static function parseGastos(array $p): array
    {
        $out = [];
        $n = count($p['gf_desc'] ?? []);
        for ($i = 0; $i < $n; $i++) {
            $d = trim($p['gf_desc'][$i] ?? '');
            $mm = (float) ($p['gf_mensual'][$i] ?? 0);
            $dd = (float) ($p['gf_diario'][$i] ?? 0);
            if ($d === '' && $mm <= 0 && $dd <= 0) continue;
            $out[] = ['descripcion' => $d ?: ('Gasto #' . ($i + 1)), 'num_integrantes' => 0, 'monto_mensual' => $mm, 'monto_diario' => $dd];
        }
        return $out;
    }

    public static function parseIngresos(array $p): array
    {
        $out = [];
        $n = max(count($p['oi_desc'] ?? []), count($p['oi_monto'] ?? []));
        for ($i = 0; $i < $n; $i++) {
            if (trim($p['oi_desc'][$i] ?? '') === '') continue;
            $out[] = ['descripcion' => trim($p['oi_desc'][$i]), 'frecuencia' => 'MENSUAL', 'num_dias' => 26,
                'monto_mensual' => (float) ($p['oi_monto'][$i] ?? 0), 'monto_diario' => (float) ($p['oi_diario'][$i] ?? 0)];
        }
        return $out;
    }

    public static function parseActivos(array $p): array
    {
        $out = [];
        $cats = ['inventario' => ['inv_desc','inv_cant','inv_monto','inv_marca','inv_modelo','inv_serie'],
                 'muebles' => ['mue_desc','mue_cant','mue_monto','mue_marca','mue_modelo','mue_serie'],
                 'inmuebles' => ['inm_desc','inm_cant','inm_monto','inm_marca','inm_modelo','inm_serie']];
        foreach ($cats as $cat => $f) {
            $n = max(count($p[$f[0]] ?? []), count($p[$f[2]] ?? []));
            for ($i = 0; $i < $n; $i++) {
                $desc = trim($p[$f[0]][$i] ?? '');
                $monto = (float) ($p[$f[2]][$i] ?? 0);
                if ($desc === '' && $monto <= 0) continue;
                $out[] = ['categoria' => $cat, 'descripcion' => $desc, 'cantidad' => max(1, (int) ($p[$f[1]][$i] ?? 1)),
                    'monto' => $monto, 'marca' => trim($p[$f[3]][$i] ?? ''), 'modelo' => trim($p[$f[4]][$i] ?? ''),
                    'serie' => trim($p[$f[5]][$i] ?? ''), 'estado' => '', 'ruta_imagen' => null];
            }
        }
        return $out;
    }

    // ── Guardado completo (cabecera + detalles) ──
    public static function guardar(array $in, int $idUsuario, $rol, $grupoExistente = null)
    {
        $data = [
            'id_cliente' => (int) ($in['id_cliente'] ?? 0),
            'id_usuario' => $idUsuario,
            'fecha_evaluacion' => self::normalizarFecha($in['fecha_evaluacion'] ?? null),
            'fecha_caducidad' => $in['fecha_caducidad'] ?? null,
            'monto_solicitado' => $in['monto_solicitado'] ?? 0, 'tem' => $in['tem'] ?? 0,
            'tipo_credito' => $in['tipo_credito'] ?? null, 'frecuencia_pago' => $in['frecuencia_pago'] ?? null,
            'numero_cuotas' => $in['numero_cuotas'] ?? 0, 'cuota_estimada' => $in['cuota_estimada'] ?? 0,
            'periodo_cuotas' => $in['periodo_cuotas'] ?? null, 'condicion' => $in['condicion'] ?? null,
            'monto_propuesto' => $in['monto_propuesto'] ?? 0, 'excedente' => $in['excedente'] ?? 0,
            'capacidad_pago' => $in['capacidad_pago'] ?? 0,
            'endeudamiento_patrimonial' => $in['endeudamiento_patrimonial'] ?? 0,
            'capital_trabajo' => $in['capital_trabajo'] ?? 0, 'resultado' => $in['resultado'] ?? null,
            'califica_credito' => $in['califica_credito'] ?? null, 'monto_atender' => $in['monto_atender'] ?? 0,
            'mora_diaria' => $in['mora_diaria'] ?? 0,
            'ingresos_simple' => $in['ingresos_simple'] ?? null, 'egresos_simple' => $in['egresos_simple'] ?? null,
            'finalizar' => !empty($in['finalizar']),
        ];
        if (!$data['id_cliente']) throw new DomainException('id_cliente requerido.');
        if ($grupoExistente) {
            EvaluacionRepository::actualizar($grupoExistente, $data);
            if (!empty($data['finalizar'])) EvaluacionRepository::finalizar($grupoExistente);
            $grupo = $grupoExistente;
        } else {
            $grupo = EvaluacionRepository::crear($data);
        }
        EvaluacionRepository::guardarDetalles($grupo, 'eval_actividades', self::parseActividades($in));
        EvaluacionRepository::guardarDetalles($grupo, 'eval_costos', self::parseCostos($in));
        EvaluacionRepository::guardarDetalles($grupo, 'eval_deudas', self::parseDeudas($in));
        EvaluacionRepository::guardarDetalles($grupo, 'eval_gastos', self::parseGastos($in));
        EvaluacionRepository::guardarDetalles($grupo, 'eval_ingresos', self::parseIngresos($in));
        $act = self::parseActivos($in);
        if ($act) EvaluacionRepository::guardarDetalles($grupo, 'eval_activos', $act);
        return $grupo;
    }

    public static function normalizarFecha($raw): string
    {
        $raw = trim((string) ($raw ?? ''));
        if ($raw === '') return date('Y-m-d H:i:s');
        $ts = strtotime(str_replace('T', ' ', $raw));
        return $ts === false ? date('Y-m-d H:i:s') : date('Y-m-d H:i:s', $ts);
    }

    // ── Evidencia WebP (origen subirEvidencia/convertirAWebp, máx 10MB, 1280px q80) ──
    public static function guardarEvidencia(array $file, $grupo, string $pref, int $idx): ?string
    {
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) return null;
        $ext = strtolower(pathinfo($file['name'] ?? '', PATHINFO_EXTENSION));
        if (!in_array($ext, ['jpg','jpeg','png','gif','webp'], true)) return null;
        if ((int) ($file['size'] ?? 0) > 10 * 1024 * 1024) return null;
        $dir = __DIR__ . '/../../../storage/evidencias/';
        if (!is_dir($dir)) @mkdir($dir, 0755, true);
        $base = $grupo . '_' . $pref . '_' . $idx . '_' . bin2hex(random_bytes(4));
        $dest = $dir . $base . '.webp';
        if (self::aWebp($file['tmp_name'], $dest)) return 'storage/evidencias/' . $base . '.webp';
        $fn = $base . '.' . ($ext === 'jpeg' ? 'jpg' : $ext);
        return @move_uploaded_file($file['tmp_name'], $dir . $fn) ? 'storage/evidencias/' . $fn : null;
    }

    private static function aWebp(string $tmp, string $dest, int $max = 1280, int $q = 80): bool
    {
        if (!function_exists('imagecreatefromstring') || !function_exists('imagewebp')) return false;
        $raw = @file_get_contents($tmp);
        if ($raw === false) return false;
        $img = @imagecreatefromstring($raw);
        if (!$img) return false;
        $w = imagesx($img); $h = imagesy($img);
        if (max($w, $h) > $max) {
            $e = $max / max($w, $h);
            $nw = max(1, (int) round($w * $e)); $nh = max(1, (int) round($h * $e));
            $r = imagecreatetruecolor($nw, $nh);
            imagealphablending($r, false); imagesavealpha($r, true);
            imagecopyresampled($r, $img, 0, 0, 0, 0, $nw, $nh, $w, $h);
            imagedestroy($img); $img = $r;
        }
        $ok = @imagewebp($img, $dest, $q);
        imagedestroy($img);
        return $ok && file_exists($dest);
    }
}
