<!DOCTYPE html>
<html>
<head>
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
        .header img {
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
        .image-container {
            page-break-after: always;
            display: flex;
            justify-content: center;
            align-items: center;
            width: 100%;
            height: 100%;
            margin: auto;
        }
        .image-container img {
            max-width: 90%;
            max-height: 90%;
            margin: auto;
            display: block;
        }

        .chip {
            display: inline-block;
            padding: 5px 10px;
            background-color: #f0f0f0;
            border-radius: 16px;
            font-size: 14px;
            color: #333;
        }
    </style>
</head>
<body>
<div class="header">
    <img src="https://s3-api.atos8.com/atos8/assets/images/logo/atos8_.png" alt="Logo">
    <h1>Relatório de Comprovantes Mensais</h1>
</div>

<div class="content">
    <table>
        <thead>
        <tr>
            <th>Campo</th>
            <th>Valor</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td>Datas Filtradas</td>
            <td>
                {{
                    implode(', ', array_map(
                        fn($date) => \Carbon\Carbon::parse($date)->translatedFormat('F/Y'),
                        $dates ?? []
                    ))
                }}
            </td>
        </tr>
        <tr>
            <td>Tipo de Entrada</td>
            <td>
                @php
                    $entryTypes = explode(',', $filters['entryTypes'] ?? '');
                @endphp

                @foreach ($entryTypes as $entryType)
                    @switch(trim($entryType))
                        @case('tithe')
                            <span class="chip">Dízimos</span>
                            @break
                        @case('designated')
                            @if ($group != null)
                                <span class="chip">Designadas</span>
                            @else
                                <span class="chip">Todos</span>
                            @endif
                            @break
                        @case('offers')
                            <span class="chip">Ofertas</span>
                            @break
                        @default
                            <span class="chip">Não especificado</span>
                    @endswitch
                @endforeach

            </td>
        </tr>
        <tr>
            <td>Grupo Eclesiástico</td>
            <td>{{ $group != null ? $group->name : '' }}</td>
        </tr>
        </tbody>
    </table>
</div>

<div class="footer">
    <p>Gerado em: {{ date('d/m/Y H:i:s') }}</p>
</div>

<div class="page-break"></div>

@foreach ($links as $link)
    @if (file_exists($link))
        <div class="image-container">
            <img src="{{ $link }}" alt="Imagem">
        </div>
    @endif
@endforeach
</body>
</html>
