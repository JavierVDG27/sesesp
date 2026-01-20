{{-- resources/views/expedientes/segunda_parte/pdf.blade.php --}}
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Expediente Técnico</title>

    <style>
        @page {
            size: letter;
            margin-top: 2.54cm;
            margin-bottom: 2.54cm;
            margin-left: 1.9cm;
            margin-right: 1.9cm;
        }

        body{
            font-family: "Arial Narrow", Arial, sans-serif;
            font-size: 12px;
            color:#000;

            /* Evita que el logo (alto 2.25cm desde top 1.30cm) se interponga con el contenido */
            padding-top: 1.20cm;
        }
        .header{
            position: fixed;
            top: -2cm; /* anclado al borde de página; vive dentro del margen superior */
            left: 0;
            right: 0;
            height: 1.25cm;
        }
        .header .wrap{ width:100%; }
        .logo{
            width: 2.04cm;
            height: 2.25cm;
            object-fit: contain;
        }

        .title14{ font-size:14px; font-weight:700; }

        .p{ text-align: justify; line-height:1.0; }
        .center{ text-align:center; }
        .right{ text-align:right; }
        .upper{ text-transform: uppercase; }

        .cover-h1{ font-size:58px; font-weight:400; }
        .cover-h2{ font-size:26px; font-weight:350; }
        .cover-partida{ font-size:16px; font-weight:700; }
        .cover-bienes{ font-size:14px; font-weight:400; }
        .cover-fasp{ font-size:18px; font-weight:400; }
        .cover-ej{ font-size:18px; font-weight:400; }

        .eps-label{ font-size:14px; font-weight:700; }
        .eps-text{ font-size:12px; font-style:italic; }

        .mt6{ margin-top:6px; }
        .mt10{ margin-top:10px; }
        .mt14{ margin-top:14px; }
        .mt18{ margin-top:18px; }

        table{ width:100%; border-collapse: collapse; }
        th, td{ border:1px solid #000; padding:6px; vertical-align: middle; }
        th{ background:#e6e6e6; font-weight:700; font-size:12px; }
        .cell10{ font-size:10px; }
        .centerMid{ text-align:center; vertical-align: middle; }
        .no-border{ border:0 !important; }

        .page-break{ page-break-after: always; }
    </style>
</head>
<body>

{{-- HEADER (todas las páginas) --}}
<div class="header">
    <table class="wrap" style="border:0;">
        <tr>
            <td class="no-border" style="width:80px;">
                <img class="logo" src="{{ public_path('images/LogoExpediente.png') }}" alt="Logo">
            </td>
            <td class="no-border"></td>
        </tr>
    </table>
</div>

@php
    $anio = $detalle->anio_override ?? null;
    $anioFinal = ($anio === null || $anio === '') ? (int)$expediente->anio_ejercicio : (int)$anio;

    $partidaGen = '';
    $bienesList = [];

    if (!empty($t6)) {
        foreach ($t6 as $r) {
            $pb = (string)($r['partida_bien_servicio'] ?? '');
            if ($pb === '') continue;

            $parts = array_map('trim', explode('/', $pb, 2));
            if ($partidaGen === '' && isset($parts[0])) $partidaGen = $parts[0];
            if (isset($parts[1]) && $parts[1] !== '') $bienesList[] = $parts[1];
        }
    }

    $bienesList = array_values(array_unique(array_filter($bienesList)));

    $bienesTexto = '';
    $cnt = count($bienesList);
    if ($cnt === 1) $bienesTexto = $bienesList[0];
    elseif ($cnt === 2) $bienesTexto = $bienesList[0].' y '.$bienesList[1];
    elseif ($cnt > 2) $bienesTexto = implode(', ', array_slice($bienesList, 0, $cnt - 1)).' y '.$bienesList[$cnt - 1];

    $marco = [];
    if (!empty($detalle->marco_legal_json)) {
        $tmp = json_decode($detalle->marco_legal_json, true);
        if (is_array($tmp)) $marco = $tmp;
    }
@endphp

@php
    // Si el controlador no mandó la variable, por defecto NO es borrador
    $esBorrador = $esBorrador ?? false;
@endphp

    {{-- Marca de agua para indicar que el expediente está en modo borrador --}}
    @if($esBorrador)
        <div style="
            position: fixed;
            top: 40%;
            left: 8%;
            right: 8%;
            text-align: center;
            opacity: 0.10;
            font-size: 80px;
            transform: rotate(-30deg);
            color: #808080;
        ">
            BORRADOR
        </div>
    @endif

<div style="page-break-after: always; padding-top:0;">
    <div style="margin-top:3cm; padding-top:0;">
        <div class="center upper cover-h1">{{ $detalle->titulo_documento }}</div>

        {{-- Salto de Linea --}}
        <div class="mt10 p">{!! nl2br(e(''))!!}</div>

        <div class="center cover-h2">{{ $detalle->subtitulo_documento }}</div>

        {{-- 2 Saltos de Linea --}}
        <div class="mt10 p">{!! nl2br(e(''))!!}{!! nl2br(e(''))!!}</div>

        <div class="mt18 center cover-partida" style="text-transform: uppercase;">{{ $partidaGen }}</div>
        <div class="mt10 center cover-bienes">{{ $bienesTexto }}</div>

        {{-- 4 Saltos de Linea --}}
        <div class="mt14 p">{!! nl2br(e(''))!!}</div>
        <div class="mt14 p">{!! nl2br(e(''))!!}</div>
        <div class="mt14 p">{!! nl2br(e(''))!!}</div>
        <div class="mt14 p">{!! nl2br(e(''))!!}</div>

        <div class="cover-h2 center cover-fasp">{{ $detalle->fasp_texto }}</div>

        <div class="mt10 center cover-ej">
            <span>{{ $detalle->ejercicio_fiscal_label }}</span>
            <span> {{ $anioFinal }}</span>
        </div>

        {{-- 2 Saltos de Linea --}}
        <div class="mt10 p">{!! nl2br(e(''))!!}{!! nl2br(e(''))!!}</div>

        <div class="mt18 right">
            <div class="mt14 eps-label">Eje</div>
            <div class="mt6 p">{!! nl2br(e(''))!!}</div>
            <div class="eps-text">{{ $epsEje ?? '' }}</div>
            <div class="mt6 p">{!! nl2br(e(''))!!}</div>

            <div class="mt14 eps-label">Programa</div>
            <div class="mt6 p">{!! nl2br(e(''))!!}</div>
            <div class="eps-text">{{ $epsProg ?? '' }}</div>
            <div class="mt6 p">{!! nl2br(e(''))!!}</div>

            <div class="mt14 eps-label">Subprograma</div>
            <div class="mt6 p">{!! nl2br(e(''))!!}</div>
            <div class="eps-text">{{ $epsSub ?? '' }}</div>
        </div>
    </div>
</div>

<div class="title14">1. Introducción</div>
<div class="mt10 p">{!! nl2br(e($detalle->introduccion ?? '')) !!}</div>

<div class="mt14 title14">2. Marco legal</div>
<div class="mt10">
    @foreach($marco as $bloque)
        @php
            $titulo = trim((string)($bloque['titulo'] ?? ''));
            $arts = (array)($bloque['articulos'] ?? []);
        @endphp

        @if($titulo !== '')
            <div class="mt10" style="font-size:12px; font-weight:700;">{{ $titulo }}</div>
        @endif

        @foreach($arts as $a)
            @php
                $art = trim((string)($a['articulo'] ?? ''));
                $desc = trim((string)($a['descripcion'] ?? ''));
                $incisos = (array)($a['incisos'] ?? []);
            @endphp

            @if($art !== '' || $desc !== '')
                <div class="p mt6">
                    @if($art !== '') <span style="font-weight:700;">{{ $art }}</span>@endif
                    @if($desc !== '') <span>{{ ($art !== '' ? '. ' : '') }}{{ $desc }}</span>@endif
                </div>
            @endif

            @foreach($incisos as $inc)
                @php
                    $rom = trim((string)($inc['romano'] ?? ''));
                    $d = trim((string)($inc['descripcion'] ?? ''));
                @endphp
                @if($rom !== '' || $d !== '')
                    <div class="p mt6" style="margin-left:18px;">
                        @if($rom !== '') <span style="font-weight:700;">{{ $rom }}.</span>@endif
                        @if($d !== '') <span> {{ $d }}</span>@endif
                    </div>
                @endif
            @endforeach
        @endforeach
    @endforeach
</div>

<div class="mt14 title14">3. Objeto</div>
<div class="mt10 p">{!! nl2br(e($detalle->objeto ?? '')) !!}</div>

<div class="mt14 title14">4. Alcance</div>
<div class="mt10 p">{!! nl2br(e($detalle->alcance ?? '')) !!}</div>

<div class="mt14 title14">5. Justificación</div>
<div class="mt10 p">{!! nl2br(e($detalle->justificacion ?? '')) !!}</div>

<div class="mt14 title14">6. Estructura Programática (transcripción presupuestal)</div>
<div class="mt10">
    <table>
        <thead>
        <tr>
            <th>Programa</th>
            <th>Subprograma</th>
            <th>Partida específica / Bien o servicio</th>
            <th>Costo del bien o servicio</th>
            <th>Unidad de Medida</th>
            <th>Metas (Cantidad)</th>
            <th>Aportación (Federal/Estatal)</th>
        </tr>
        </thead>
        <tbody>
        @foreach($t6 as $r)
            <tr class="cell10">
                <td>{{ $r['programa'] ?? '' }}</td>
                <td>{{ $r['subprograma'] ?? '' }}</td>
                <td>{{ $r['partida_bien_servicio'] ?? '' }}</td>
                <td class="right">{{ number_format((float)($r['costo'] ?? 0), 2) }}</td>
                <td class="centerMid">{{ $r['unidad_medida'] ?? '' }}</td>
                <td class="centerMid">{{ $r['meta_cantidad'] ?? '' }}</td>
                <td class="centerMid">{{ $r['aportacion'] ?? '' }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>

<div class="mt14 title14">7. Especificaciones Técnicas</div>
<div class="mt10">
    <table>
        <thead>
        <tr>
            <th style="width:8%;">Partida</th>
            <th>Descripción Técnica (características mínimas)</th>
            <th style="width:8%;">Cantidad</th>
            <th style="width:12%;">Unidad de Medida</th>
            <th style="width:12%;">Precio Unitario</th>
            <th style="width:12%;">Importe sin IVA</th>
        </tr>
        </thead>
        <tbody>
        @php $subtotal = 0.0; @endphp
        @foreach($t7 as $r)
            @php
                $importe = (float)($r['importe_sin_iva'] ?? 0);
                $subtotal += $importe;

                $titulo = (string)($r['titulo_producto'] ?? '');
                $raw = $r['descripcion_tecnica'] ?? '[]';
                $arr = is_array($raw) ? $raw : (json_decode((string)$raw, true) ?: []);
            @endphp
            <tr class="cell10">
                <td class="centerMid">{{ $r['partida'] ?? '' }}</td>
                <td>
                    @if(trim($titulo) !== '')
                        <div style="font-size:12px; font-weight:700;" class="upper">{{ $titulo }}</div>
                    @endif

                    @foreach($arr as $it)
                        @php
                            $tipo = $it['tipo'] ?? '';
                            $texto = (string)($it['texto'] ?? '');
                        @endphp
                        @if($tipo === 'subtitulo')
                            <div style="font-size:10px; font-weight:700;" class="upper mt6">{{ $texto }}</div>
                        @else
                            <div style="font-size:10px;" class="p">{{ $texto }}</div>
                        @endif
                    @endforeach
                </td>
                <td class="centerMid">{{ $r['cantidad'] ?? 0 }}</td>
                <td class="centerMid">{{ $r['unidad_medida'] ?? '' }}</td>
                <td class="right">{{ $r['precio_unitario'] === null ? '' : number_format((float)$r['precio_unitario'], 2) }}</td>
                <td class="right">{{ number_format($importe, 2) }}</td>
            </tr>
        @endforeach

        @php
            $iva = round($subtotal * 0.16, 2);
            $total = round($subtotal + $iva, 2);
        @endphp
        <tr>
            <td class="no-border" colspan="4"></td>
            <td class="right" style="font-weight:700;">Subtotal</td>
            <td class="right" style="font-weight:700;">{{ number_format($subtotal, 2) }}</td>
        </tr>
        <tr>
            <td class="no-border" colspan="4"></td>
            <td class="right" style="font-weight:700;">IVA</td>
            <td class="right" style="font-weight:700;">{{ number_format($iva, 2) }}</td>
        </tr>
        <tr>
            <td class="no-border" colspan="4"></td>
            <td class="right" style="font-weight:700;">Total</td>
            <td class="right" style="font-weight:700;">{{ number_format($total, 2) }}</td>
        </tr>
        </tbody>
    </table>
</div>

<div class="mt14 title14">8. Entregables del Proyecto de Inversión</div>
<div class="mt10">
    @php $n = max(1, count($t8)); @endphp
    <table>
        <thead>
        <tr>
            <th style="width:6%;">Núm.</th>
            <th>Descripción</th>
            <th style="width:10%;">Cantidad</th>
            <th style="width:20%;">Fecha de Entrega</th>
            <th style="width:22%;">Responsable de Validar el Entregable</th>
            <th style="width:20%;">Lugar de Entrega</th>
        </tr>
        </thead>
        <tbody>
        @foreach($t8 as $idx => $r)
            <tr class="cell10">
                <td class="centerMid">{{ $r['num'] ?? $r['orden'] ?? ($idx+1) }}</td>
                <td class="centerMid">{{ $r['descripcion'] ?? '' }}</td>
                <td class="centerMid">{{ $r['cantidad'] ?? 0 }}</td>

                @if($idx === 0)
                    <td class="centerMid" rowspan="{{ $n }}">{!! nl2br(e($detalle->tabla8_fecha_entrega ?? '')) !!}</td>
                    <td class="centerMid" rowspan="{{ $n }}">{!! nl2br(e($detalle->tabla8_responsable_validar ?? '')) !!}</td>
                    <td class="centerMid" rowspan="{{ $n }}">{!! nl2br(e($detalle->tabla8_lugar_entrega ?? '')) !!}</td>
                @endif
            </tr>
        @endforeach
        </tbody>
    </table>
</div>

@php
    $secciones = [
        9  => ['Muestras', $detalle->no_aplica_9 ?? ''],
        10 => ['Recursos Humanos', $detalle->no_aplica_10 ?? ''],
        11 => ['Soporte Técnico', $detalle->no_aplica_11 ?? ''],
        12 => ['Mantenimiento', $detalle->no_aplica_12 ?? ''],
        13 => ['Capacitación, Actualización y Puesta en Marcha', $detalle->no_aplica_13 ?? ''],
        14 => ['Vigencia', $detalle->no_aplica_14 ?? ''],
        15 => ['Criterio de Evaluación', $detalle->no_aplica_15 ?? ''],
        16 => ['Indicador de Medición', $detalle->no_aplica_16 ?? ''],
        17 => ['Forma de Pago', $detalle->no_aplica_17 ?? ''],
        18 => ['Garantías', $detalle->no_aplica_18 ?? ''],
        19 => ['Formato para que el proveedor presente sus Propuestas Técnicas y Económicas', $detalle->no_aplica_19 ?? ''],
    ];
@endphp

@foreach($secciones as $num => [$titulo, $texto])
    <div class="mt14 title14">{{ $num }}. {{ $titulo }}</div>
    <div class="mt10 p">{!! nl2br(e((string)$texto)) !!}</div>
@endforeach

<div class="mt14 title14">20. Documentos Anexos</div>

@php
    $anexos = preg_split("/\r\n|\n|\r/", $detalle->no_aplica_20 ?? '');
@endphp

<div class="mt10">
    @foreach($anexos as $line)
        @php
            $line = trim($line);
            if ($line === '') continue;

            // Detecta "Anexo X."
            if (preg_match('/^(Anexo\s+\d+\.)\s*(.*)$/i', $line, $m)) {
                $head = $m[1]; // Anexo X.
                $tail = $m[2] ?? '';
            } else {
                $head = null;
                $tail = $line;
            }
        @endphp

        <div class="p" style="font-size:12px;">
            @if($head)
                <span style="font-weight:700;">{{ $head }}</span>
                @if($tail)
                    <span> {{ $tail }}</span>
                @endif
            @else
                {{ $tail }}
            @endif
        </div>
    @endforeach
</div>


<div class="page-break"></div>

<div class="title14">21. Validación del Expediente Técnico</div>
<div class="mt10 center upper" style="font-size:12px; font-weight:700;">RESPONSABLES DEL PROYECTO</div>

<table class="mt10">
    <tr>
        <th class="center">Responsable del Subprograma</th>
        <th class="center">Titular de la Dependencia</th>
    </tr>
    <tr style="height:260px;">
        <td class="centerMid">
            <div style="margin-top:160px;">
                <div style="font-size:12px; font-weight:700;">{{ $detalle->responsable_subprograma_nombre ?? '' }}</div>
                <div style="font-size:12px;">{{ $detalle->responsable_subprograma_cargo ?? '' }}</div>
            </div>
        </td>
        <td class="centerMid">
            <div style="margin-top:160px;">
                <div style="font-size:12px; font-weight:700;">{{ $detalle->titular_dependencia_nombre ?? '' }}</div>
                <div style="font-size:12px;">{{ $detalle->titular_dependencia_cargo ?? '' }}</div>
            </div>
        </td>
    </tr>
</table>

<script type="text/php">
if (isset($pdf)) {
    $pdf->page_script(function ($pageNumber, $pageCount, $canvas, $fontMetrics) {

        $fontN = $fontMetrics->getFont("Helvetica", "normal");
        $fontB = $fontMetrics->getFont("Helvetica", "bold");
        $size = 12;

        $w = $canvas->get_width();
        $h = $canvas->get_height();

        $t1 = "Hoja ";
        $t3 = " de ";

        // Anchos
        $w1 = $fontMetrics->getTextWidth($t1, $fontN, $size);
        $w3 = $fontMetrics->getTextWidth($t3, $fontN, $size);
        $wNum = $fontMetrics->getTextWidth((string)$pageNumber, $fontB, $size);
        $wCnt = $fontMetrics->getTextWidth((string)$pageCount, $fontB, $size);

        $total = $w1 + $wNum + $w3 + $wCnt;

        // Centrado horizontal
        $x = ($w - $total) / 2;
        $y = $h - 45;

        $canvas->text($x, $y, $t1, $fontN, $size);
        $canvas->text($x + $w1, $y, (string)$pageNumber, $fontB, $size);
        $canvas->text($x + $w1 + $wNum, $y, $t3, $fontN, $size);
        $canvas->text($x + $w1 + $wNum + $w3, $y, (string)$pageCount, $fontB, $size);
    });
}
</script>

</body>
</html>