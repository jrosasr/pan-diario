@php
    $team = $delivery->team;
    $church = $delivery->church;
@endphp
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Entrega #{{ $delivery->id }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }

        .header {
            width: 100%;
            display: flex;
            flex-direction: row;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 40px;
            position: relative;
        }

        .header-left {
            display: flex;
            flex-direction: row;
            align-items: flex-start;
        }

        .team-img {
            width: 75px;
            height: 75px;
            object-fit: cover;
            border-radius: 20px;
            border: 1px solid #8f8f8f;
            margin-right: 20px;
        }

        .team-info {
            margin-top: 10px;
        }

        .header-right {
            text-align: right;
            margin-top: 10px;
        }

        .header-center {
            position: absolute;
            left: 50%;
            top: 60px;
            transform: translateX(-50%);
            font-size: 1.3em;
        }

        .section {
            margin-bottom: 20px;
            padding: 15px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th,
        td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
        }

        th {
            background: #f5f5f5;
        }

        .signature {
            margin-top: 40px;
            text-align: center;
        }

        .footer {
            position: fixed;
            left: 0;
            bottom: 0;
            width: 100%;
            text-align: right;
            font-size: 0.9em;
            color: #888;
        }

        /* El contenedor principal debe tener un ancho definido */
        .contenedor-principal {
            width: 95%; /* Ocupa el 95% del ancho del documento */
            margin-left: auto;
            margin-right: auto;
            /* Equivalente a margin: 0 auto; */

            /* Agrega aquí las propiedades de maquetación que ya funcionan */
            overflow: hidden; /* Limpia los floats */
        }

        .contenedor-principal::after {
            content: "";
            display: table;
            clear: both;
        }

        .contenedor-hijo {
            height: 80px;
            /* Altura para el ejemplo */
            float: left;
            /* La clave: coloca el elemento a la izquierda */
        }

        .caja-1 {
            width: 80px;
            /* background-color: #ff5733; */
        }

        .caja-2 {
            width: calc(100% - 80px - 80px);
            padding-left: 5px;
            /* Calcula el ancho restante: 100 - 15 - 15 = 70 */
            /*  background-color: #33aaff; */
        }

        .caja-3 {
            width: 80px;
            /* background-color: #33ff57; */
        }


        /* Firmas */
        .sign-box {
            width: 95%; /* Ocupa el 95% del ancho del documento */
            margin-left: auto;
            margin-right: auto;
            /* Equivalente a margin: 0 auto; */

            /* Agrega aquí las propiedades de maquetación que ya funcionan */
            overflow: hidden; /* Limpia los floats */
        }

        .sign-box::after {
            content: "";
            display: table;
            clear: both;
        }

        .sign-box .single-box {
            height: 300px;
            /* Altura para el ejemplo */
            float: left;
            /* La clave: coloca el elemento a la izquierda */
        }

        .sign-box-1 {
            width: 50%;
            margin-left: 13%
            /* background-color: #ff5733; */
        }

        .sign-box-2 {
            width: 50%;
            /* background-color: #ff5733; */
        }

    </style>
</head>

<body>
    <!-- Header estructurado -->
    <div class="contenedor-principal">
        <div class="contenedor-hijo caja-1">
            @if ($team && $team->logo)
                @php
                    $imgPath = $team->logo;
                    if (str_starts_with($imgPath, 'team/')) {
                        $imgPath = public_path('storage/' . $imgPath);
                    } elseif (str_starts_with($imgPath, 'storage/')) {
                        $imgPath = public_path($imgPath);
                    } elseif (str_starts_with($imgPath, '/')) {
                        $imgPath = public_path(ltrim($imgPath, '/'));
                    } else {
                        $imgPath = public_path('storage/' . $imgPath);
                    }
                @endphp
                <img src="{{ $imgPath }}" class="team-img" alt="Imagen del equipo">
            @else
                <div class="team-img"></div>
            @endif
        </div>
        <div class="contenedor-hijo caja-2">
            <div style="font-size:1.2em; padding-top: 5px;"><strong>{{ $team?->name }}</strong></div>
            <div>{{ $team?->address }}</div>
            <h3>Entrega #{{ $delivery->id }}</h3>
        </div>
        <div class="contenedor-hijo caja-3">
            <div>{{ $delivery->created_at->format('d/m/Y') }}</div>
        </div>
    </div>
    <div class="section">
        <div><strong>Iglesia:</strong> {{ $church?->name }}</div>
        <div><strong>Pastor:</strong> {{ $church?->pastor_name }}</div>
        <div><strong>CI:</strong> {{ $church?->identification_number }}</div>
        <div><strong>Dirección:</strong> {{ $church?->address }}</div>
    </div>
    <div class="section">
        <table>
            <thead>
                <tr>
                    <th>Insumos</th>
                    <th>Cantidad</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($delivery->resources as $resource)
                    <tr>
                        <td>{{ $resource->name }}</td>
                        <td>{{ $resource->pivot->quantity ?? '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <br>
    <br>

    <div class="sign-box">
        <div class="single-box sign-box-1">
            <p>Quien entrega:</p>
                @if($delivery->signature_deliverer)
                    <img src="{{ public_path('storage/' . $delivery->signature_deliverer) }}" style="width:150px; height:60px; border:1px solid #ccc;" alt="Firma entregador">
                @else
                    <p>______________________________</p>
                @endif
                <p style="margin-top:5px;">{{ $delivery->deliverer_name }}</p>
        </div>
        <div class="single-box sign-box-2">
            <p>Firma del receptor:</p>
                @if($delivery->signature_beneficiary)
                    <img src="{{ public_path('storage/' . $delivery->signature_beneficiary) }}" style="width:150px; height:60px; border:1px solid #ccc;" alt="Firma beneficiario">
                @else
                    <p>______________________________</p>
                @endif
                <p>{{ $church?->name }}</p>
                <p>Pastor: {{ $church?->pastor_name }}</p>
                <p>CI: {{ $church?->identification_number }}</p>
        </div>
    </div>

    <!-- Footer con número de página -->
    <div class="footer">
        <script type="text/php">
            if (isset($pdf)) {
                $pdf->page_script('if ($PAGE_COUNT > 1) { $font = $fontMetrics->get_font("Arial", "normal"); $size = 10; $pdf->text(520, 820, "Página " . $PAGE_NUM . " de " . $PAGE_COUNT, $font, $size); }');
            }
        </script>
    </div>
</body>

</html>
