<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Evaluación Crediticia #<?= $eval['id_evaluacion'] ?></title>
    <style>
        @page { margin: 15mm; size: A4; }
        body { font-family: 'Times New Roman', Times, serif; font-size: 12pt; color: #000; line-height: 1.5; }
        h1 { text-align: center; font-size: 16pt; margin-bottom: 4px; }
        h2 { font-size: 13pt; border-bottom: 1px solid #333; padding-bottom: 4px; margin-top: 18px; }
        .subtitle { text-align: center; font-size: 10pt; color: #555; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin: 8px 0; font-size: 10pt; }
        th, td { border: 1px solid #333; padding: 4px 6px; text-align: left; }
        th { background: #e0e0e0; font-weight: bold; }
        .result-box { text-align: center; padding: 12px; border: 2px solid #333; margin: 16px 0; font-size: 14pt; font-weight: bold; }
        .result-box.aprobado { border-color: #28a745; color: #28a745; }
        .result-box.rechazado { border-color: #dc3545; color: #dc3545; }
        .result-box.riesgo_medio { border-color: #ffc107; color: #9a7b04; }
        .text-right { text-align: right; }
        .no-print { display: none; }
        .footer { text-align: center; font-size: 9pt; color: #888; margin-top: 30px; border-top: 1px solid #ccc; padding-top: 8px; }
    </style>
</head>
<body>
    <?php
    // Unidad del plazo según la frecuencia (el N° de cuotas está en esas unidades)
    $unidadPlazo = function ($ev) {
        $n = (int) ($ev['numero_cuotas'] ?? 0);
        $fcc = strtoupper(trim((string) ($ev['periodo_cuotas'] ?? '')));
        $fq = strtoupper(trim((string) ($ev['frecuencia_pago'] ?? '')));
        if ($fcc !== '' && !is_numeric($fcc)) {
            if (strpos($fcc, 'Q') === 0 || strpos($fcc, 'QUINCENAL') !== false) return $n === 1 ? 'quincena' : 'quincenas';
            if (strpos($fcc, 'S') === 0 || strpos($fcc, 'SEMANAL') !== false) return $n === 1 ? 'semana' : 'semanas';
            if (strpos($fcc, 'M') === 0 || strpos($fcc, 'MENSUAL') !== false) return $n === 1 ? 'mes' : 'meses';
            if (strpos($fcc, 'D') === 0 || strpos($fcc, 'DIARIO') !== false) return $n === 1 ? 'día' : 'días';
        }
        if (strpos($fq, 'QUINCENAL') !== false) return $n === 1 ? 'quincena' : 'quincenas';
        if (strpos($fq, 'SEMANAL') !== false) return $n === 1 ? 'semana' : 'semanas';
        if (strpos($fq, 'MENSUAL') !== false) return $n === 1 ? 'mes' : 'meses';
        return $n === 1 ? 'día' : 'días';
    };
    ?>
    <h1>EVALUACIÓN CREDITICIA</h1>
    <div class="subtitle">N° <?= $eval['id_evaluacion'] ?> — Fecha: <?= date('d/m/Y', strtotime($eval['fecha_evaluacion'])) ?></div>

    <h2>Datos del Cliente</h2>
    <table>
        <tr><td style="width:25%"><strong>Cliente:</strong></td><td colspan="3"><?= htmlspecialchars($eval['nom'] . ' ' . $eval['ap'] . ' ' . $eval['am']) ?></td></tr>
        <tr><td><strong>DNI:</strong></td><td><?= htmlspecialchars($eval['dni']) ?></td><td><strong>Celular:</strong></td><td><?= htmlspecialchars($eval['cel'] ?? '') ?></td></tr>
        <tr><td><strong>Dirección:</strong></td><td colspan="3"><?= htmlspecialchars($eval['direc'] ?? '') ?></td></tr>
        <tr><td><strong>Rubro:</strong></td><td><?= htmlspecialchars($eval['rubro'] ?? '') ?></td><td><strong>Condición:</strong></td><td><?= htmlspecialchars($eval['condicion'] ?? '') ?></td></tr>
    </table>

    <h2>Detalle del Crédito</h2>
    <table>
        <tr><td><strong>Monto Solicitado:</strong></td><td>S/ <?= number_format($eval['monto_solicitado'], 2) ?></td><td><strong>TEM:</strong></td><td><?= ($eval['tem'] * 100) ?>%</td></tr>
        <tr><td><strong>Frecuencia:</strong></td><td><?= htmlspecialchars($eval['frecuencia_pago']) ?></td><td><strong>Plazo:</strong></td><td><?= $eval['numero_cuotas'] ?> <?= $unidadPlazo($eval) ?></td></tr>
        <tr><td><strong>Cuota Estimada:</strong></td><td>S/ <?= number_format($eval['cuota_estimada'], 2) ?></td><td><strong>Tipo Crédito:</strong></td><td><?= htmlspecialchars($eval['tipo_credito'] ?? '') ?></td></tr>
        <tr><td><strong>Monto Propuesto:</strong></td><td>S/ <?= number_format($eval['monto_propuesto'] ?? 0, 2) ?></td><td><strong>Monto Atender:</strong></td><td>S/ <?= number_format($eval['monto_atender'] ?? 0, 2) ?></td></tr>
    </table>

    <h2>Indicadores Financieros</h2>
    <table>
        <tr><th>Indicador</th><th>Valor</th><th>Resultado</th></tr>
        <tr><td>Excedente</td><td>S/ <?= number_format($eval['excedente'], 2) ?></td><td><?= $eval['excedente'] >= $eval['cuota_estimada'] ? 'APROBADO' : 'NO APROBADO' ?></td></tr>
        <tr><td>Capacidad de Pago</td><td><?= number_format($eval['capacidad_pago'], 2) ?>%</td><td><?= $eval['capacidad_pago'] < 85 ? 'CALIFICA' : 'NO CALIFICA' ?></td></tr>
        <tr><td>Endeudamiento Patrimonial</td><td><?= number_format($eval['endeudamiento_patrimonial'], 2) ?>%</td><td><?= $eval['endeudamiento_patrimonial'] < 100 ? 'CALIFICA' : 'NO CALIFICA' ?></td></tr>
        <tr><td>Capital de Trabajo</td><td>S/ <?= number_format($eval['capital_trabajo'], 2) ?></td><td><?= $eval['capital_trabajo'] >= 0 ? 'CALIFICA' : 'ALERTA' ?></td></tr>
        <tr><td>Mora Diaria</td><td colspan="2">S/ <?= number_format($eval['mora_diaria'] ?? 0, 2) ?></td></tr>
    </table>

    <?php if (!empty($eval['activos_detalle'])): ?>
    <h2>Detalle de Activos</h2>
    <?php
    $cats = ['inventario' => 'Inventario', 'muebles' => 'Muebles y Enseres', 'inmuebles' => 'Inmueble, Maq. y Equipo'];
    foreach ($cats as $key => $label):
        $items = array_filter($eval['activos_detalle'], fn($a) => ($a['categoria'] ?? '') === $key);
        if (empty($items)) continue;
    ?>
    <h3 style="font-size:11pt;margin:8px 0 2px;"><?= $label ?></h3>
    <table>
        <thead><tr><th>Descripción</th><th>Cantidad</th><th class="text-right">Monto S/.</th></tr></thead>
        <tbody>
            <?php foreach ($items as $item): ?>
            <tr>
                <td><?= htmlspecialchars($item['descripcion'] ?? '') ?></td>
                <td><?= (int)($item['cantidad'] ?? 1) ?></td>
                <td class="text-right"><?= number_format($item['monto'] ?? 0, 2) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php endforeach; ?>
    <?php endif; ?>

    <div class="result-box <?= $eval['resultado'] ?: 'rechazado' ?>">
        RESULTADO: <?= strtoupper(str_replace('_', ' ', $eval['resultado'] ?? 'PENDIENTE')) ?>
    </div>

    <br><br>
    <table style="border:none;margin-top:40px;">
        <tr style="border:none;">
            <td style="border:none;text-align:center;">
                _________________________________<br>
                <strong>Analista / Asesor</strong><br>
                <?= htmlspecialchars($_SESSION['user_name'] ?? '') ?>
            </td>
            <td style="border:none;text-align:center;">
                _________________________________<br>
                <strong>V°B° Administración</strong>
            </td>
        </tr>
    </table>

    <div class="footer">Documento generado el <?= date('d/m/Y H:i') ?> — CENTECP</div>

    <script>window.print();</script>
</body>
</html>