@php
    use Carbon\Carbon;Carbon::setLocale('pt_BR');

    $path = public_path('img/logo/atos8_.png');
    $type = pathinfo($path, PATHINFO_EXTENSION);
    $contentImage = file_get_contents($path);
    $logo = 'data:image/' . $type . ';base64,' . base64_encode($contentImage);
@endphp
    <!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Dizimistas - {{ $month }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #333;
        }

        .header {
            text-align: center;
            padding: 20px;
            border-bottom: 2px solid #ccc;
        }

        .header svg {
            height: 80px;
        }

        .header h1 {
            margin-top: 10px;
        }

        .content {
            padding: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            font-size: 14px;
        }

        th, td {
            border: 1px solid #ccc;
            padding: 10px;
        }

        thead {
            display: table-header-group;
        }

        tbody {
            display: table-row-group;
        }

        th {
            background-color: #f8f8f8;
            text-align: left;
        }

        .footer {
            text-align: center;
            padding: 10px;
            border-top: 2px solid #ccc;
            margin-top: 20px;
            font-size: 12px;
            color: #666;
        }

        .page-break {
            page-break-after: always;
        }

        .svg-modal {
            margin: 30px auto 0 auto;
            display: flex;
            justify-content: center;
        }
    </style>
</head>
<body>
<div class="header">
    <img src="{{ $logo }}" style="width: 200px;" alt="Logo">
    <h1>Relatório de dizimistas</h1>
    <h1>{{ ucfirst($month) }}</h1>
</div>
<div class="content">
    <table>
        <tbody>
            <tr>
                <td>Mês</td>
                <td>{{ ucfirst($month) }}</td>
            </tr>
            <tr>
                <td>Dizimistas</td>
                <td>{{ $qtdTithers }}</td>
            </tr>
            <tr>
                <td>Total</td>
                <td>R$ {{ number_format($totalTithes, 2, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Alvo mensal</td>
                <td>R$ {{ number_format($monthlyTarget, 2, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Porcentagem</td>
                <td>{{ number_format(($totalTithes / $monthlyTarget) * 100, 2, ',', '.') }}%</td>
            </tr>
        </tbody>
    </table>
</div>
<div class="footer">
    <p>Gerado em: {{ date('d/m/Y H:i:s') }}</p>
</div>
<div class="page-break"></div>

<table>
    <thead style="display: table-header-group;">
        <tr>
            <th>Nome completo</th>
        </tr>
    </thead>
    <tbody>
    @foreach($data as $row)
        <tr>
            <td>{{ $row->fullName ?? '' }}</td>
        </tr>
    @endforeach
    </tbody>
</table>

<div class="footer">
    Relatório gerado em {{ date('d/m/Y H:i:s') }}
</div>
</body>
</html>
