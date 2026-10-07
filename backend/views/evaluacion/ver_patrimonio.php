<?php
// DECLARACIÓN JURADA PATRIMONIAL - Réplica fiel del formato físico (PDF original)
// Ruta: /evaluacion/exportarPatrimonio/{grupo}
$esc = function ($v) { return htmlspecialchars(trim((string)($v ?? '')), ENT_QUOTES, 'UTF-8'); };
$nomTit = trim(($eval['nom'] ?? '') . ' ' . ($eval['ap'] ?? '') . ' ' . ($eval['am'] ?? ''));
$dniTit = trim((string)($eval['dni'] ?? ''));
$con = (isset($eval['conyuge']) && is_array($eval['conyuge'])) ? $eval['conyuge'] : null;
$nomCon = $con ? trim(($con['nom'] ?? '') . ' ' . ($con['ap'] ?? '') . ' ' . ($con['am'] ?? '')) : '';
$dniCon = $con ? trim((string)($con['dni'] ?? '')) : '';
$domicilio = trim((string)($eval['direc'] ?? ''));
$distrito = trim((string)($eval['distrito'] ?? ''));
$provincia = trim((string)($eval['provincia'] ?? ''));
$lugarDom = trim(trim($domicilio . ($distrito !== '' ? ', ' . $distrito : ''), ', '));
$ciudad = $provincia !== '' ? $provincia : $distrito;
$ingresos = isset($eval['ingresos_simple']) && trim((string)$eval['ingresos_simple']) !== '' ? number_format((float)$eval['ingresos_simple'], 2) : null;
$mesesEs = [1 => 'enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'];
$ahora = time();
$bienes = [];
if (!empty($eval['activos_detalle']) && is_array($eval['activos_detalle'])) {
    foreach ($eval['activos_detalle'] as $b) {
        if (!is_array($b)) continue;
        // Sin espacios en blanco: solo bienes con descripción
        if (trim((string)($b['descripcion'] ?? '')) === '') continue;
        $bienes[] = $b;
    }
}
$totalGeneral = 0;
foreach ($bienes as $b) $totalGeneral += (float)($b['monto'] ?? 0);
$totalGeneralFmt = number_format($totalGeneral, 2);
$imgUrl = function ($ruta) {
    $ruta = trim((string)($ruta ?? ''));
    if ($ruta === '') return '';
    if (preg_match('#^https?://#i', $ruta)) return $ruta;
    if (strpos($ruta, 'data:') === 0) return $ruta;
    $base = defined('URL') ? rtrim(URL, '/') : '';
    // ruta guardada es 'uploads/evidencias/xxx.webp' relativa a evaluador/
    return $base . '/evaluador/' . ltrim($ruta, '/');
};
$fotosBien = function ($item) {
    $fotos = [];
    $ri = $item['ruta_imagen'] ?? '';
    if (is_array($ri)) {
        foreach ($ri as $u) { if (trim((string)$u) !== '') $fotos[] = trim((string)$u); }
    } elseif (is_string($ri) && trim($ri) !== '') {
        $dec = json_decode($ri, true);
        if (is_array($dec)) {
            foreach ($dec as $u) { if (trim((string)$u) !== '') $fotos[] = trim((string)$u); }
        } else {
            $fotos[] = trim($ri);
        }
    }
    return $fotos;
};
$gruposAnexo = ['inventario' => [], 'muebles' => [], 'inmuebles' => []];
$requiereSeleccion = false;
foreach ($bienes as $item) {
    $cat = strtolower(trim((string)($item['categoria'] ?? '')));
    if (!isset($gruposAnexo[$cat])) continue;
    $fotos = [];
    foreach ($fotosBien($item) as $fu) {
        $u = $imgUrl($fu);
        if ($u !== '') $fotos[] = $u;
    }
    if (empty($fotos)) continue;
    if (count($fotos) > 4) $requiereSeleccion = true;
    $gruposAnexo[$cat][] = [
        'desc' => trim((string)($item['descripcion'] ?? '')),
        'marca' => trim((string)($item['marca'] ?? '')),
        'modelo' => trim((string)($item['modelo'] ?? '')),
        'serie' => trim((string)($item['serie'] ?? '')),
        'estado' => trim((string)($item['estado'] ?? '')),
        'monto' => (float)($item['monto'] ?? 0),
        'fotos' => $fotos
    ];
}
$hayAnexos = !empty($gruposAnexo['inventario']) || !empty($gruposAnexo['muebles']) || !empty($gruposAnexo['inmuebles']);
$linea = function ($texto) use ($esc) {
    $texto = trim((string)$texto);
    if ($texto !== '') return '<strong>'.$esc($texto).'</strong>';
    return '<span style="display:inline-block;min-width:220px;border-bottom:1px dotted #000;">&nbsp;</span>';
};
$lineaCorta = function ($texto) use ($esc) {
    $texto = trim((string)$texto);
    if ($texto !== '') return '<strong>'.$esc($texto).'</strong>';
    return '<span style="display:inline-block;min-width:110px;border-bottom:1px dotted #000;">&nbsp;</span>';
};
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Declaración Jurada Patrimonial - <?= $esc($nomTit) ?></title>
<style>
    @page { size: A4; margin: 0; }
    * { box-sizing: border-box; }
    body { font-family: 'Times New Roman', Times, serif; font-size: 11pt; color: #000; margin: 0; line-height: 1.45; }
    .membrete { display: block; width: 100%; }
    .membrete-bottom { margin-top: 45mm; }
    .contenido { padding: 2mm 15mm 24mm 15mm; }
    @media print {
        .membrete { position: fixed; left: 0; right: 0; width: 100%; z-index: 0; }
        .membrete-top { top: 0; }
        .membrete-bottom { bottom: 0; margin-top: 0; }
        .contenido { padding-top: 42mm; padding-bottom: 20mm; }
        .salto-pagina + .item-caja, .salto-pagina + h2.anexo-sub, .salto-pagina + .pagina-cajas { margin-top: 42mm; }
    }
    h1 { text-align: center; font-size: 14pt; font-weight: bold; text-transform: uppercase; margin: 10px 0 12px 0; letter-spacing: 0.5px; }
    p { text-align: justify; margin: 7px 0; font-size: 11pt; }
    table.inv { width: 100%; border-collapse: collapse; font-size: 9.5pt; margin: 10px 0; }
    table.inv th, table.inv td { border: 1px solid #000; padding: 4px 5px; text-align: left; vertical-align: top; height: 20px; }
    table.inv th { font-weight: bold; text-align: center; background: #f0f0f0; font-size: 8.5pt; }
    .text-center { text-align: center; }
    .text-right { text-align: right; }
    table.firmas { width: 100%; margin-top: 40px; border: none; page-break-inside: avoid; }
    table.firmas td { border: none; text-align: center; vertical-align: bottom; font-size: 10pt; }
    .firma-line { border-top: 1px solid #000; width: 220px; margin: 0 auto; padding-top: 4px; font-size: 10pt; }
    .anexo-sub { font-size: 11pt; font-weight: bold; text-transform: uppercase; margin: 12px 0 4px 0; border-bottom: 1px solid #000; padding-bottom: 2px; page-break-after: avoid; }
    .foto-celda.vacia { border: none; }
    .pagina-cajas { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
    .pagina-cajas > h2.anexo-sub { grid-column: 1 / -1; }
    .item-caja { border: 1px solid #000; padding: 6px; background: #fff; page-break-inside: avoid; }
    .item-caja.span1 { grid-column: span 1; }
    .item-caja.span2 { grid-column: 1 / -1; }
    .item-fotos { display: grid; gap: 6px; }
    .item-fotos.n1, .item-fotos.n2 { grid-template-columns: 1fr; }
    .item-fotos.n3, .item-fotos.n4 { grid-template-columns: 1fr 1fr; }
    .item-fotos img { width: 100%; aspect-ratio: 1 / 1; object-fit: cover; display: block; background: #fff; }
    .item-datos { margin-top: 5px; line-height: 1.4; }
    .item-desc { font-size: 11pt; }
    .item-datos .item-det { font-size: 9.5pt; color: #222; }
    .salto-pagina { page-break-before: always; }
    .sel-panel { background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 10px; padding: 12px 14px; margin: 12px 0; font-family: Arial, sans-serif; }
    .sel-panel h3 { font-family: Arial, sans-serif; }
    .pie { text-align: center; font-size: 7.5pt; color: #555; margin-top: 25px; border-top: 1px solid #ccc; padding-top: 6px; page-break-inside: avoid; }
    .salto { page-break-before: always; }
    @media print { .no-print { display: none !important; } .pf { box-shadow: none; } }
    .barra { background: #0b3d91; color: #fff; padding: 8px 12px; display: flex; gap: 8px; align-items: center; font-family: Arial, sans-serif; position: sticky; top: 0; z-index: 100; }
    .barra button, .barra a { background: #fff; color: #0b3d91; border: 0; padding: 6px 12px; border-radius: 6px; font-weight: bold; cursor: pointer; text-decoration: none; font-size: 9pt; }
</style>
</head>
<body>
<div class="barra no-print">
    <button onclick="window.print()">Imprimir / Guardar PDF</button>
    <a href="javascript:window.close()">Cerrar</a>
    <span style="font-size:8.5pt;">DJ Patrimonial - <?= $esc($nomTit) ?> - DOI: <?= $esc($dniTit) ?></span>
    <span id="estado-imagenes" style="font-size:8.5pt;font-style:italic;"></span>
</div>

<?php
$membBase = rtrim(defined('URL') ? URL : '', '/') . '/assets/img/membrete/';
$membTop = $membBase . 'header_centecp.png';
$membBottom = $membBase . 'footer_centecp.png';
?>
<img class="membrete membrete-top" src="<?= htmlspecialchars($membTop) ?>" alt="Membrete CENTECP">
<img class="membrete membrete-bottom" src="<?= htmlspecialchars($membBottom) ?>" alt="">

<div class="contenido">
<h1>DECLARACIÓN JURADA PATRIMONIAL</h1>

<p>Yo, <?= $nomTit !== '' ? '<strong>'.$esc($nomTit).'</strong>' : $linea('') ?>, identificado(a) con DOI N° <?= $dniTit !== '' ? '<strong>'.$esc($dniTit).'</strong>' : $lineaCorta('') ?><?php if ($nomCon !== '' || $dniCon !== ''): ?> y <?= $nomCon !== '' ? '<strong>'.$esc($nomCon).'</strong>' : $linea('') ?>, identificado(a) con DOI N° <?= $dniCon !== '' ? '<strong>'.$esc($dniCon).'</strong>' : $lineaCorta('') ?><?php endif; ?>, domiciliado(s) en <?= $lugarDom !== '' ? $esc($lugarDom) : $linea('') ?>, en la ciudad de <?= $ciudad !== '' ? $esc($ciudad) : $lineaCorta('') ?>.</p>

<p>Declaro(amos) bajo juramento que los bienes que debajo detallo(amos) son de mi(nuestra) exclusiva propiedad y libre disposición, los mismos que otorgo(amos) en primera y preferencial garantía, a favor de <strong>CENTRO DE TECNOLOGÍA Y CRÉDITO DEL PERÚ E.I.R.L.</strong>, por el(los) préstamo(s) directo(s) o indirecto(s) que mantengo(amos) vigente(s) y cuyo(s) contrato(s) de mutuo acuerdo hemos firmado por separado. Declaro(amos) que la valorización de los bienes ha sido hecha de común acuerdo entre las partes y me(nos) comprometo(emos) a no enajenar dichos bienes, mientras exista la obligación contraída.</p>

<p>Así mismo, declaro(amos) que mis(nuestros) ingresos promedios mensuales ascienden a S/ <strong><?= $totalGeneralFmt ?></strong>.</p>

<table class="inv">
    <thead><tr><th style="width:6%">ÍTEM</th><th>DESCRIPCIÓN DE BIEN</th><th style="width:12%">MARCA</th><th style="width:12%">MODELO</th><th style="width:12%">SERIE</th><th style="width:10%">ESTADO</th><th style="width:16%">VALOR EN GARANTÍA S/.</th></tr></thead>
    <tbody>
        <?php $n = 1; foreach ($bienes as $item): ?>
        <tr>
            <td class="text-center"><?= str_pad($n++, 2, '0', STR_PAD_LEFT) ?></td>
            <td><?= $esc($item['descripcion'] ?? '') ?><?= ((int)($item['cantidad'] ?? 1) > 1) ? ' (Cant. '.(int)$item['cantidad'].')' : '' ?></td>
            <td><?= $esc($item['marca'] ?? '') ?></td>
            <td><?= $esc($item['modelo'] ?? '') ?></td>
            <td><?= $esc($item['serie'] ?? '') ?></td>
            <td><?= $esc($item['estado'] ?? '') ?></td>
            <td class="text-right"><?= number_format($item['monto'] ?? 0, 2) ?></td>
        </tr>
        <?php endforeach; ?>
        <tr>
            <td style="border:none;"></td><td style="border:none;"></td><td style="border:none;"></td><td style="border:none;"></td><td style="border:none;"></td>
            <td style="font-weight:bold; text-align:center; border:1px solid #000;">S/.</td>
            <td style="text-align:right;font-weight:bold; border:1px solid #000;"><?= number_format($totalGeneral, 2) ?></td>
        </tr>
    </tbody>
</table>

<p>Fecha: <?= $esc($ciudad !== '' ? $ciudad : '..............') ?>, <?= date('d', $ahora) ?> de <?= $esc($mesesEs[(int)date('n', $ahora)]) ?> del <?= date('Y', $ahora) ?></p>

<table class="firmas">
    <tr>
        <td>
            <br><br>
            <div class="firma-line">Deudor / Aval</div>
            <br>
            <?= $esc($nomTit) ?><br>
            DOI: <?= $esc($dniTit) ?>
        </td>
        <td>
            <br><br>
            <div class="firma-line">Cónyuge Deudor / Aval</div>
            <br>
            <?= $nomCon !== '' ? $esc($nomCon) . '<br>DOI: ' . $esc($dniCon) : '<br>DOI: ' ?>
        </td>
    </tr>
</table>
</div>

<?php if ($hayAnexos): ?>
<div class="salto contenido contenido-anexo">
<h1 style="font-size:13pt;">ANEXO: FOTOS DE BIENES EN GARANTÍA</h1>
<p class="text-center" style="font-size:10pt;">Evaluación N° <?= $eval['id_evaluacion'] ?> — <?= $esc($nomTit) ?> — DOI: <?= $esc($dniTit) ?></p>
<?php if ($requiereSeleccion): ?>
<div class="sel-panel no-print">
    <h3 style="margin:0 0 8px 0;font-size:11pt;">Selecciona las fotos a imprimir (máximo 4 por bien)</h3>
    <p style="font-size:9pt;margin:0 0 10px 0;">Hay bienes con más de 4 fotos. Marca las 4 que deseas imprimir de cada uno y luego pulsa <strong>Imprimir</strong>.</p>
    <?php foreach ($gruposAnexo as $catS => $listaS): ?>
        <?php foreach ($listaS as $idxS => $axS): ?>
            <?php if (count($axS['fotos']) <= 4) continue; ?>
            <div class="sel-grupo" data-sel="sel-<?= $catS ?>-<?= $idxS ?>">
                <div style="font-weight:bold;font-size:10pt;margin:6px 0 4px 0;"><?= htmlspecialchars($axS['desc']) ?> (<?= count($axS['fotos']) ?> fotos)</div>
                <?php foreach ($axS['fotos'] as $kS => $fotoS): ?>
                    <label style="display:inline-block;margin:3px;text-align:center;cursor:pointer;">
                        <input type="checkbox" value="<?= htmlspecialchars($fotoS) ?>" <?= $kS < 4 ? 'checked' : '' ?> style="display:block;margin:0 auto 3px auto;">
                        <img src="<?= htmlspecialchars($fotoS) ?>" alt="" style="width:90px;height:90px;object-fit:cover;border:2px solid <?= $kS < 4 ? '#2563EB' : '#ccc' ?>;border-radius:6px;">
                    </label>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>
    <?php endforeach; ?>
    <div style="margin-top:10px;"><button onclick="window.print()" style="background:#0b3d91;color:#fff;border:0;padding:8px 18px;border-radius:6px;font-weight:bold;cursor:pointer;">Imprimir / Guardar PDF</button></div>
</div>
<?php endif; ?>
<?php
$nombresCat = ['inventario' => 'Inventario', 'muebles' => 'Muebles y Enseres', 'inmuebles' => 'Inmuebles, Maquinaria y Equipos'];
// Cada bien = una caja con sus fotos (máx 4) y sus datos UNA sola vez.
// Se empaquetan cajas por cantidad de fotos (máx 4 por página), sin partir cajas.
$cajasT = [];
foreach ($gruposAnexo as $catG => $listaG) {
    foreach ($listaG as $idxG => $axG) {
        $fotos4 = array_slice($axG['fotos'], 0, 4);
        if (empty($fotos4)) continue;
        $cajasT[] = ['cat' => $catG, 'idx' => $idxG, 'ax' => $axG, 'fotos' => $fotos4, 'n' => count($fotos4)];
    }
}
$paginasCajasT = []; $pagActC = []; $fotosPag = 0;
foreach ($cajasT as $cajaT) {
    if (!empty($pagActC) && $fotosPag + $cajaT['n'] > 4) { $paginasCajasT[] = $pagActC; $pagActC = []; $fotosPag = 0; }
    $pagActC[] = $cajaT;
    $fotosPag += $cajaT['n'];
}
if (!empty($pagActC)) $paginasCajasT[] = $pagActC;
?>
<?php $ultCatT = null; ?>
<?php foreach ($paginasCajasT as $piT => $cajasP): ?>
    <?php if ($piT > 0) echo '<div class="salto-pagina"></div>'; ?>
    <div class="pagina-cajas">
    <?php foreach ($cajasP as $cajaP): ?>
        <?php if ($cajaP['cat'] !== $ultCatT): ?>
            <h2 class="anexo-sub"><?= $nombresCat[$cajaP['cat']] ?></h2>
            <?php $ultCatT = $cajaP['cat']; ?>
        <?php endif; ?>
        <div class="item-caja<?= $cajaP['n'] >= 3 ? ' span2' : ' span1' ?>">
            <div class="item-fotos n<?= min($cajaP['n'], 4) ?>">
            <?php foreach ($cajaP['fotos'] as $kC => $urlC): ?>
                <img src="<?= htmlspecialchars($urlC) ?>" alt="<?= htmlspecialchars($cajaP['ax']['desc']) ?>"<?= count($cajaP['ax']['fotos']) > 4 ? ' data-sel="sel-'.$cajaP['cat'].'-'.$cajaP['idx'].'" data-slot="'.$kC.'"' : '' ?>>
            <?php endforeach; ?>
            </div>
            <div class="item-datos">
                <strong class="item-desc"><?= htmlspecialchars($cajaP['ax']['desc']) ?></strong><br>
                <span class="item-det">Marca: <?= htmlspecialchars($cajaP['ax']['marca'] ?: '-') ?> | Modelo: <?= htmlspecialchars($cajaP['ax']['modelo'] ?: '-') ?> | Serie: <?= htmlspecialchars(($cajaP['ax']['serie'] ?? '') ?: '-') ?><br>Estado: <?= htmlspecialchars($cajaP['ax']['estado'] ?: '-') ?> | Precio: S/ <?= number_format($cajaP['ax']['monto'], 2) ?></span>
            </div>
        </div>
    <?php endforeach; ?>
    </div>
<?php endforeach; ?>
</div>
<?php else: ?>
<p style="text-align:center; color:#888; margin-top:20px; font-style:italic;">Sin fotos registradas para inventario/muebles/inmuebles en esta evaluación.</p>
<?php endif; ?>

<p class="pie">Documento generado electrónicamente el <?= date('d/m/Y H:i') ?> — CENTRO DE TECNOLOGÍA Y CRÉDITOS DEL PERÚ</p>

<script>
window.REQUIERE_SELECCION = <?= $requiereSeleccion ? 'true' : 'false' ?>;
(function () {
    var impreso = false;
    var aviso = document.getElementById('estado-imagenes');
    function imprimir() { if (impreso) return; impreso = true; setTimeout(function () { window.print(); }, 600); }
    function listo(m) { if (aviso) aviso.textContent = m; }

    // Sincroniza la selección de fotos (máx 4 por bien) con las fotos a imprimir
    document.querySelectorAll('.sel-grupo').forEach(function (grupo) {
        var boxes = grupo.querySelectorAll('input[type="checkbox"]');
        boxes.forEach(function (box) {
            box.addEventListener('change', function () {
                var checked = grupo.querySelectorAll('input[type="checkbox"]:checked');
                if (checked.length > 4) {
                    box.checked = false;
                    alert('Solo puedes elegir hasta 4 fotos por bien.');
                    return;
                }
                if (checked.length === 0) {
                    box.checked = true;
                    alert('Elige al menos 1 foto.');
                    return;
                }
                var selId = grupo.getAttribute('data-sel');
                var urls = Array.prototype.map.call(checked, function (c) { return c.value; });
                var imgs = document.querySelectorAll('img[data-sel="' + selId + '"]');
                imgs.forEach(function (im, i) {
                    if (i < urls.length) {
                        im.src = urls[i];
                        im.style.display = '';
                    } else {
                        im.style.display = 'none';
                    }
                });
                boxes.forEach(function (b) {
                    var th = b.parentElement.querySelector('img');
                    if (th) th.style.borderColor = b.checked ? '#2563EB' : '#ccc';
                });
            });
        });
    });

    function esperarImagenes() {
        if (window.REQUIERE_SELECCION) {
            listo('Selecciona las fotos a imprimir y pulsa Imprimir');
            return;
        }
        var imgs = Array.prototype.slice.call(document.images);
        var pend = imgs.length, fin = false;
        listo('Cargando ' + pend + ' imágenes… espere antes de imprimir');
        if (!pend) { listo('Listo para imprimir'); imprimir(); return; }
        function una() { if (fin) return; pend--; if (pend <= 0) { fin = true; listo('Listo para imprimir'); imprimir(); } else { listo('Cargando imágenes… (' + pend + ' restantes)'); } }
        imgs.forEach(function (im) {
            if (im.complete) { una(); }
            else { im.addEventListener('load', una); im.addEventListener('error', una); }
        });
        setTimeout(function () { if (!fin) { fin = true; listo('Listo para imprimir'); imprimir(); } }, 25000);
    }
    if (document.readyState === 'complete') { esperarImagenes(); }
    else { window.addEventListener('load', esperarImagenes); if (!window.REQUIERE_SELECCION) { setTimeout(imprimir, 25000); } }
})();
</script>
</body>
</html>
