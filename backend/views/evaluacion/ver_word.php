<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:w="urn:schemas-microsoft-com:office:word" xmlns="http://www.w3.org/TR/REC-html40">
<head>
<meta charset="UTF-8">
<title>Declaración Jurada de Patrimonio</title>
<style>
    @page { size: A4; margin: 2.5cm 2cm 2cm 2cm; }
    body { font-family: 'Times New Roman', Times, serif; font-size: 12pt; line-height: 1.5; }
    h1 { text-align: center; font-size: 14pt; font-weight: bold; text-transform: uppercase; margin-bottom: 4px; }
    h2 { font-size: 11pt; font-weight: bold; margin-top: 14px; margin-bottom: 4px; border-bottom: 1px solid #000; padding-bottom: 2px; }
    p { text-align: justify; margin: 6px 0; }
    table { width: 100%; border-collapse: collapse; font-size: 10pt; margin: 8px 0; }
    th, td { border: 1px solid #000; padding: 4px 6px; text-align: left; vertical-align: top; }
    th { font-weight: bold; }
    .text-center { text-align: center; }
    .text-right { text-align: right; }
    .firma { margin-top: 40px; text-align: center; }
    .firma-line { border-top: 1px solid #000; width: 250px; margin: 0 auto; padding-top: 4px; }
</style>
</head>
<body>
    <h1>DECLARACIÓN JURADA DE PATRIMONIO</h1>
    <p style="text-align:center;font-size:10pt;">
        <?= strtoupper(htmlspecialchars($eval['nom'] . ' ' . $eval['ap'] . ' ' . $eval['am'])) ?> — 
        DNI: <?= htmlspecialchars($eval['dni']) ?>
    </p>
    <p style="text-align:center;font-size:10pt;">
        EVALUACIÓN CREDITICIA N° <?= $eval['id_evaluacion'] ?> — 
        FECHA: <?= date('d/m/Y', strtotime($eval['fecha_evaluacion'])) ?>
    </p>

    <p>Yo, <strong><?= htmlspecialchars($eval['nom'] . ' ' . $eval['ap'] . ' ' . $eval['am']) ?></strong>, identificado con DNI N° <strong><?= htmlspecialchars($eval['dni']) ?></strong>, declaro bajo juramento que los bienes detallados a continuación son de mi propiedad y que los valores declarados se ajustan a la realidad, asumiendo plena responsabilidad por la veracidad de la información proporcionada.</p>

    <?php if (!empty($eval['activos_detalle'])): 
        $cats = ['inventario' => 'Inventario', 'muebles' => 'Muebles y Enseres', 'inmuebles' => 'Inmueble, Maquinaria y Equipo'];
        $totalGeneral = 0;
    ?>
    <h2>DETALLE DE BIENES</h2>
    <?php foreach ($cats as $key => $label):
        $items = array_filter($eval['activos_detalle'], fn($a) => ($a['categoria'] ?? '') === $key);
        if (empty($items)) continue;
        $subtotal = array_sum(array_column($items, 'monto'));
        $totalGeneral += $subtotal;
    ?>
    <h3 style="font-size:10pt;margin:6px 0 2px;"><?= $label ?></h3>
    <table>
        <thead><tr><th style="width:8%">N°</th><th>Descripción</th><th style="width:10%">Cantidad</th><th style="width:18%">Monto S/.</th></tr></thead>
        <tbody>
            <?php $n = 1; foreach ($items as $item): ?>
            <tr>
                <td class="text-center"><?= $n++ ?></td>
                <td><?= htmlspecialchars($item['descripcion'] ?? '') ?></td>
                <td class="text-center"><?= (int)($item['cantidad'] ?? 1) ?></td>
                <td class="text-right"><?= number_format($item['monto'] ?? 0, 2) ?></td>
            </tr>
            <?php endforeach; ?>
            <tr>
                <td colspan="3" style="text-align:right;font-weight:bold;">Subtotal <?= $label ?>:</td>
                <td style="text-align:right;font-weight:bold;">S/ <?= number_format($subtotal, 2) ?></td>
            </tr>
        </tbody>
    </table>
    <?php endforeach; ?>

    <?php if (count(array_filter($eval['activos_detalle'], fn($a) => in_array($a['categoria'] ?? '', ['equipos','vehiculos','otros']))) > 0): 
        $otros = array_filter($eval['activos_detalle'], fn($a) => in_array($a['categoria'] ?? '', ['equipos','vehiculos','otros']));
        $subOtros = array_sum(array_column($otros, 'monto'));
        $totalGeneral += $subOtros;
    ?>
    <h3 style="font-size:10pt;margin:6px 0 2px;">Otros Activos</h3>
    <table>
        <thead><tr><th style="width:8%">N°</th><th>Descripción</th><th style="width:10%">Cantidad</th><th style="width:18%">Monto S/.</th></tr></thead>
        <tbody>
            <?php $n = 1; foreach ($otros as $item): ?>
            <tr>
                <td class="text-center"><?= $n++ ?></td>
                <td><?= htmlspecialchars($item['descripcion'] ?? '') ?></td>
                <td class="text-center"><?= (int)($item['cantidad'] ?? 1) ?></td>
                <td class="text-right"><?= number_format($item['monto'] ?? 0, 2) ?></td>
            </tr>
            <?php endforeach; ?>
            <tr>
                <td colspan="3" style="text-align:right;font-weight:bold;">Subtotal Otros:</td>
                <td style="text-align:right;font-weight:bold;">S/ <?= number_format($subOtros, 2) ?></td>
            </tr>
        </tbody>
    </table>
    <?php endif; ?>

    <?php else: ?>
    <p style="font-style:italic;">No se registraron bienes en esta evaluación.</p>
    <?php endif; ?>

    <table style="margin-top:16px;">
        <tr>
            <td style="width:60%;border:none;"><strong>TOTAL PATRIMONIO DECLARADO:</strong></td>
            <td style="width:40%;border:1px solid #000;text-align:right;font-weight:bold;font-size:12pt;">
                S/ <?= number_format($totalGeneral ?? 0, 2) ?>
            </td>
        </tr>
    </table>

    <p style="margin-top:20px;">Declaro que la información proporcionada es verídica y que he sido informado de las sanciones civiles y penales que conlleva la falsedad en esta declaración jurada, de conformidad con el Artículo 411 del Código Penal.</p>

    <table style="border:none;margin-top:50px;">
        <tr style="border:none;">
            <td style="border:none;text-align:center;width:50%;">
                <br><br>
                <div class="firma-line" style="width:220px;">FIRMA DEL DECLARANTE</div>
                <br>
                <?= htmlspecialchars($eval['nom'] . ' ' . $eval['ap'] . ' ' . $eval['am']) ?><br>
                DNI: <?= htmlspecialchars($eval['dni']) ?>
            </td>
            <td style="border:none;text-align:center;width:50%;">
                <br><br>
                <div class="firma-line" style="width:220px;">V°B° ASESOR / ANALISTA</div>
                <br>
                <?= htmlspecialchars($_SESSION['user_name'] ?? '') ?>
            </td>
        </tr>
    </table>

    <p style="text-align:center;font-size:8pt;margin-top:30px;color:#888;">Documento generado electrónicamente el <?= date('d/m/Y H:i') ?> — CENTRO DE TECNOLOGÍA Y CRÉDITOS DEL PERÚ</p>
</body>
</html>