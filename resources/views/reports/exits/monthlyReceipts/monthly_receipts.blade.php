@php use Carbon\Carbon; @endphp
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Relatório de Comprovantes Mensais</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'card': '#ffffff',
                        'accent': '#f9fafb',
                        'secondary': '#6b7280'
                    },
                    fontFamily: {
                        'inter': ['Inter', 'sans-serif']
                    }
                }
            }
        }
    </script>
    <style>
        @page {
            size: A4;
            margin: 10mm;
        }

        * {
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        body {
            margin: 0;
            padding: 0;
        }

        .page-break {
            page-break-after: always;
            break-after: page;
        }

        .image-container {
            page-break-after: always;
            break-after: page;
            text-align: center;
            padding: 20px;
            min-height: 800px;
        }

        .image-container img {
            max-width: 90%;
            max-height: 1000px;
            width: auto;
            height: auto;
        }

        .chip {
            display: inline-block;
            padding: 5px 10px;
            background-color: #f0f0f0;
            border-radius: 16px;
            font-size: 14px;
            color: #333;
            margin-right: 5px;
        }
    </style>
</head>
<body>
<div class="inline-block text-left p-0">
    <div class="bg-card w-[980px] w-auto rounded-none bg-transparent shadow-none">
        <div class="w-full">
            <div class="grid grid-cols-2 gap-6 mb-6">
                <div class="bg-gray-200 rounded-xl p-6">
                    <div class="font-inter">
                        <div class="flex items-start gap-4 mb-4">
                            <div class="flex-shrink-0">
                                <div class="w-16 h-16 bg-white rounded-xl flex items-center justify-center">
                                    <img src="https://angular-material.fusetheme.com/images/logo/logo.svg" class="w-12">
                                </div>
                            </div>
                            <div class="flex-grow">
                                <h2 class="text-lg font-bold text-gray-800">{{ $churchData->name }}</h2>
                            </div>
                        </div>
                        <div class="text-sm text-gray-700 space-y-2">
                            <div>{{ $churchData->address }}</div>
                            <div><span class="font-semibold">Contato:</span> {{ $churchData->cellPhone }}</div>
                            <div><span class="font-semibold">CNPJ:</span> {{ $churchData->docNumber }}</div>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-200 rounded-xl p-6">
                    <div class="h-full flex flex-col justify-between font-inter">
                        <div class="mb-4">
                            <div class="text-lg font-bold text-gray-800">Relatório de Comprovantes Mensais</div>
                        </div>
                        <div class="space-y-2 text-sm">
                            <div class="flex items-center gap-2">
                                <span class="text-gray-700 font-semibold">Tipo de Saída:</span>
                                @php
                                    $exitTypes = explode(',', $filters['exitTypes'] ?? '');
                                    $exitTypesCount = count(array_filter($exitTypes, fn($type) => !empty(trim($type))));
                                @endphp
                                @if($exitTypesCount === 4)
                                    <span class="bg-gray-400 text-white px-3 py-1 rounded-full text-xs font-semibold">Todos</span>
                                @else
                                    @foreach ($exitTypes as $exitType)
                                        @switch(trim($exitType))
                                            @case('payments')
                                                <span class="bg-gray-400 text-white px-3 py-1 rounded-full text-xs font-semibold">Pagamentos</span>
                                                @break
                                            @case('transfer')
                                                <span class="bg-gray-400 text-white px-3 py-1 rounded-full text-xs font-semibold">Transferências</span>
                                                @break
                                            @case('ministerial_transfer')
                                                <span class="bg-gray-400 text-white px-3 py-1 rounded-full text-xs font-semibold">Repasse Ministerial</span>
                                                @break
                                            @case('contributions')
                                                <span class="bg-gray-400 text-white px-3 py-1 rounded-full text-xs font-semibold">Contribuições</span>
                                                @break
                                            @default
                                                <span class="bg-gray-400 text-white px-3 py-1 rounded-full text-xs font-semibold">Não especificado</span>
                                        @endswitch
                                    @endforeach
                                @endif
                            </div>
                            <div class="text-gray-700">
                                <span class="font-semibold">Período:</span>
                                <span class="capitalize">
                                    @if(count($dates) > 0)
                                        {{ implode(', ', array_map(fn($date) => Carbon::parse($date)->locale('pt_BR')->isoFormat('MMM/YYYY'), $dates)) }}
                                    @else
                                        -
                                    @endif
                                </span>
                            </div>
                            <div class="text-gray-700">
                                <span class="font-semibold">Data de Emissão:</span> {{ date('d/m/Y') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-gray-200 rounded-xl p-6">
                <div class="flex flex-wrap gap-6 items-center justify-between">
                    <div class="flex flex-wrap gap-6 items-center">
                        @if($exitTypesAmount['paymentsAmount'] > 0)
                            <div>
                                <div class="text-secondary text-[14px] font-medium tracking-tight mb-2">PAGAMENTOS</div>
                                <div class="font-semibold text-gray-800 text-lg">
                                    R$ {{ number_format($exitTypesAmount['paymentsAmount'], 2, ',', '.') }}
                                </div>
                            </div>
                        @endif
                        @if($exitTypesAmount['transfersAmount'] > 0)
                            <div>
                                <div class="text-secondary text-[14px] font-medium tracking-tight mb-2">TRANSFERÊNCIAS</div>
                                <div class="font-semibold text-gray-800 text-lg">
                                    R$ {{ number_format($exitTypesAmount['transfersAmount'], 2, ',', '.') }}
                                </div>
                            </div>
                        @endif
                        @if($exitTypesAmount['ministerialTransferAmount'] > 0)
                            <div>
                                <div class="text-secondary text-[14px] font-medium tracking-tight mb-2">REPASSE MINISTERIAL</div>
                                <div class="font-semibold text-gray-800 text-lg">
                                    R$ {{ number_format($exitTypesAmount['ministerialTransferAmount'], 2, ',', '.') }}
                                </div>
                            </div>
                        @endif
                        @if($exitTypesAmount['contributionsAmount'] > 0)
                            <div>
                                <div class="text-secondary text-[14px] font-medium tracking-tight mb-2">CONTRIBUIÇÕES</div>
                                <div class="font-semibold text-gray-800 text-lg">
                                    R$ {{ number_format($exitTypesAmount['contributionsAmount'], 2, ',', '.') }}
                                </div>
                            </div>
                        @endif
                    </div>
                    <div class="pl-6 border-l border-gray-300">
                        <div class="text-secondary text-[14px] font-medium tracking-tight mb-2">TOTAL</div>
                        <div class="font-semibold text-gray-800 text-lg">
                            R$ {{ number_format($exitTypesAmount['paymentsAmount'] + $exitTypesAmount['transfersAmount'] + $exitTypesAmount['ministerialTransferAmount'] + $exitTypesAmount['contributionsAmount'], 2, ',', '.') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="page-break"></div>

@foreach ($links as $link)
    @if (file_exists($link))
        <div class="image-container">
            <img src="{{ $link }}" alt="Comprovante">
        </div>
    @endif
@endforeach
</body>
</html>
