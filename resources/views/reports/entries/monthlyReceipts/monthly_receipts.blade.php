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
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 20px;
        }

        .image-container img {
            max-width: 90%;
            max-height: 900px;
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
                                <h2 class="text-lg font-bold text-gray-800">{{ $reportData->churchData->name }}</h2>
                            </div>
                        </div>
                        <div class="text-sm text-gray-700 space-y-2">
                            <div>{{ $reportData->churchData->address }}</div>
                            <div><span class="font-semibold">Contato:</span> {{ $reportData->churchData->cellPhone }}</div>
                            <div><span class="font-semibold">CNPJ:</span> {{ $reportData->churchData->docNumber }}</div>
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
                                <span class="text-gray-700 font-semibold">Tipo de Entrada:</span>
                                @php
                                    $entryTypes = explode(',', $filters['entryTypes'] ?? '');
                                    $entryTypesCount = count(array_filter($entryTypes, fn($type) => !empty(trim($type))));
                                @endphp
                                @if($entryTypesCount === 3)
                                    <span class="bg-gray-400 text-white px-3 py-1 rounded-full text-xs font-semibold">Todos</span>
                                @else
                                    @foreach ($entryTypes as $entryType)
                                        @switch(trim($entryType))
                                            @case('tithe')
                                                <span class="bg-gray-400 text-white px-3 py-1 rounded-full text-xs font-semibold">Dízimos</span>
                                                @break
                                            @case('designated')
                                                <span class="bg-gray-400 text-white px-3 py-1 rounded-full text-xs font-semibold">Designadas</span>
                                                @break
                                            @case('offer')
                                                <span class="bg-gray-400 text-white px-3 py-1 rounded-full text-xs font-semibold">Ofertas</span>
                                                @break
                                            @default
                                                <span class="bg-gray-400 text-white px-3 py-1 rounded-full text-xs font-semibold">Não especificado</span>
                                        @endswitch
                                    @endforeach
                                @endif
                            </div>
                            @if($group != null)
                                <div class="flex items-center gap-2">
                                    <span class="text-gray-700 font-semibold">Grupo:</span>
                                    <span class="bg-gray-400 text-white px-3 py-1 rounded-full text-xs font-semibold">{{ $group->name }}</span>
                                </div>
                            @endif
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
                        @if($entryTypesAmount['titheAmount'] > 0)
                            <div>
                                <div class="text-secondary text-[14px] font-medium tracking-tight mb-2">DÍZIMOS</div>
                                <div class="font-semibold text-gray-800 text-lg">
                                    R$ {{ number_format($entryTypesAmount['titheAmount'], 2, ',', '.') }}
                                </div>
                            </div>
                        @endif
                        @if($entryTypesAmount['offerAmount'] > 0)
                            <div>
                                <div class="text-secondary text-[14px] font-medium tracking-tight mb-2">OFERTAS</div>
                                <div class="font-semibold text-gray-800 text-lg">
                                    R$ {{ number_format($entryTypesAmount['offerAmount'], 2, ',', '.') }}
                                </div>
                            </div>
                        @endif
                        @if($entryTypesAmount['designatedAmount'] > 0)
                            <div>
                                <div class="text-secondary text-[14px] font-medium tracking-tight mb-2">DESIGNADAS</div>
                                <div class="font-semibold text-gray-800 text-lg">
                                    R$ {{ number_format($entryTypesAmount['designatedAmount'], 2, ',', '.') }}
                                </div>
                            </div>
                        @endif
                    </div>
                    <div class="pl-6 border-l border-gray-300">
                        <div class="text-secondary text-[14px] font-medium tracking-tight mb-2">TOTAL</div>
                        <div class="font-semibold text-gray-800 text-lg">
                            R$ {{ number_format($entryTypesAmount['titheAmount'] + $entryTypesAmount['designatedAmount'] + $entryTypesAmount['offerAmount'], 2, ',', '.') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="page-break"></div>

@foreach ($links as $receipt)
    @php
        $localPath = is_array($receipt) ? ($receipt['localPath'] ?? null) : $receipt;
        $transactionType = is_array($receipt) ? ($receipt['transactionType'] ?? 'pix') : 'pix';
        $entryType = is_array($receipt) ? ($receipt['entryType'] ?? null) : null;
        $amount = is_array($receipt) ? ($receipt['amount'] ?? 0) : 0;
        $dateCompensation = is_array($receipt) ? ($receipt['dateCompensation'] ?? null) : null;

        // Definir cor e texto da badge baseado no tipo de entrada
        $badgeColor = 'bg-gray-500';
        $badgeText = 'Entrada';
        if ($entryType === 'designated') {
            $badgeColor = 'bg-red-500';
            $badgeText = 'Designada';
        } elseif ($entryType === 'tithe') {
            $badgeColor = 'bg-purple-500';
            $badgeText = 'Dízimo';
        } elseif ($entryType === 'offer') {
            $badgeColor = 'bg-green-500';
            $badgeText = 'Oferta';
        }
    @endphp
    @if ($localPath && file_exists($localPath))
        <div class="image-container">
            @if ($transactionType === 'cash')
                <div class="bg-gray-200 rounded-xl p-4 mb-4 inline-block w-auto">
                    <div class="flex items-center gap-8 font-inter">
                        <div class="flex items-center gap-2">
                            <span class="{{ $badgeColor }} text-white px-3 py-1 rounded-full text-xs font-semibold">{{ $badgeText }}</span>
                        </div>
                        @if ($dateCompensation)
                            <div>
                                <div class="text-secondary text-[12px] font-medium tracking-tight">DATA DE COMPENSAÇÃO</div>
                                <div class="font-semibold text-gray-800 text-base">
                                    {{ Carbon::parse($dateCompensation)->format('d/m/Y') }}
                                </div>
                            </div>
                        @endif
                        <div>
                            <div class="text-secondary text-[12px] font-medium tracking-tight">VALOR DA ENTRADA</div>
                            <div class="font-semibold text-gray-800 text-base">
                                R$ {{ number_format($amount, 2, ',', '.') }}
                            </div>
                        </div>
                    </div>
                </div>
            @endif
            <img src="{{ $localPath }}" alt="Comprovante">
        </div>
    @endif
@endforeach
</body>
</html>
