@php use Carbon\Carbon; @endphp
    <!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Invoice</title>
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

        .break-inside-avoid {
            break-inside: avoid;
            page-break-inside: avoid;
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
                                <h2 class="text-lg font-bold text-gray-800">{{ $reportData->churchData->name  }}</h2>
                            </div>
                        </div>
                        <div class="text-sm text-gray-700 space-y-2">
                            <div>Av E S/N, 4ª etapa, Rio Doce - Olinda</div>
                            <div><span class="font-semibold">Contato:</span> (81) 999002020</div>
                            <div><span class="font-semibold">CNPJ:</span>{{ $reportData->churchData->docNumber }}</div>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-200 rounded-xl p-6">
                    <div class="h-full flex flex-col justify-between font-inter">
                        <div class="mb-4">
                            <div class="text-lg font-bold text-gray-800">Relatório financeiro de entradas mensal</div>
                        </div>
                        <div class="space-y-2 text-sm">
                            <div class="flex items-center gap-2">
                                <span class="text-gray-700 font-semibold">Tipo de conta:</span>
                                <span class="bg-gray-400 text-white px-3 py-1 rounded-full text-xs font-semibold">
                                    {{ $reportData->reportInfo->accountType === 'savings_account' ? 'Poupança' : ($reportData->reportInfo->accountType === 'checking_account' ? 'Corrente' : $reportData->reportInfo->accountType) }}
                                </span>
                            </div>
                            <div class="text-gray-700">
                                <span class="font-semibold">Banco: </span>{{ $reportData->reportInfo->bankName }}
                            </div>
                            <div class="text-gray-700">
                                <span class="font-semibold">Agência: </span>{{ $reportData->reportInfo->agencyNumber }}
                            </div>
                            <div class="text-gray-700">
                                <span class="font-semibold">Conta: </span>{{ $reportData->reportInfo->accountNumber }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-gray-200 rounded-xl p-6">
                <div class="grid grid-cols-4 gap-6">
                    <div>
                        <div class="text-secondary text-[14px] font-medium tracking-tight mb-2">MÊS</div>
                        <div class="font-semibold text-gray-800 text-lg capitalize">{{ Carbon::parse($reportData->generalReportData->period)->locale('pt_BR')->isoFormat('MMMM/YYYY') }}</div>
                    </div>
                    <div>
                        <div class="text-secondary text-[14px] font-medium tracking-tight mb-2">DATA DE EMISSÃO</div>
                        <div class="font-semibold text-gray-800 text-lg">{{ $reportData->generalReportData->generationDate }}</div>
                    </div>
                    <div>
                        <div class="text-secondary text-[14px] font-medium tracking-tight mb-2">TOTAL DE ENTRADAS</div>
                        <div class="font-semibold text-gray-800 text-lg">
                            R$ {{ number_format($reportData->generalReportData->totalEntries, 2, ',', '.') }}</div>
                    </div>
                    <div>
                        <div class="text-secondary text-[14px] font-medium tracking-tight mb-2">LANÇAMENTOS</div>
                        <div
                            class="font-semibold text-gray-800 text-lg">{{ $reportData->generalReportData->quantity }}</div>
                    </div>
                </div>
            </div>

        </div>
        <div class="mt-8 mb-8 bg-gray-100 rounded-xl break-inside-avoid">
            <div class="bg-gray-200 rounded-t-xl px-6 py-4 grid grid-cols-11 gap-x-1">
                <div class="text-secondary col-span-8 text-lg font-medium">ENTRADAS</div>
                <div class="text-secondary text-right text-lg font-medium">QTD</div>
                <div class="text-secondary col-span-2 text-right text-lg font-medium">TOTAL</div>
            </div>
            <div class="px-6 py-4">
                <div class="grid grid-cols-11 gap-x-1 gap-y-4">
                    @php
                        $entriesMap = [
                            'tithes' => 'Dízimos',
                            'offers' => 'Ofertas',
                            'designated' => 'Designadas'
                        ];
                        $totalAmount = 0;
                    @endphp

                    @foreach($entriesMap as $key => $label)
                        @if(isset($reportData->entriesData->$key))
                            <div class="col-span-8 text-md font-medium">{{ $label }}</div>
                            <div class="self-center text-right">{{ $reportData->entriesData->$key->qtd }}</div>
                            <div class="col-span-2 self-center text-right">R$ {{ number_format($reportData->entriesData->$key->total, 2, ',', '.') }}</div>
                            <div class="col-span-11 border-b border-gray-300"></div>
                            @php $totalAmount += $reportData->entriesData->$key->total; @endphp
                        @endif
                    @endforeach

                    @if(isset($monthlyReportObject->includeAnonymousOffers) && $monthlyReportObject->includeAnonymousOffers)
                        <div class="col-span-8 text-md font-medium">Ofertas anônimas</div>
                        <div class="self-center text-right">-</div>
                        <div class="col-span-2 self-center text-right">-</div>
                        <div class="col-span-11 border-b border-gray-300"></div>
                    @endif

                    @if(isset($monthlyReportObject->includeTransfersBetweenAccounts) && $monthlyReportObject->includeTransfersBetweenAccounts)
                        <div class="col-span-8 text-md font-medium">Transferências entre contas</div>
                        <div class="self-center text-right">-</div>
                        <div class="col-span-2 self-center text-right">-</div>
                        <div class="col-span-11 border-b border-gray-300 my-2"></div>
                    @endif

                    <div class="col-span-11 text-right">
                        <span class="text-lg font-bold text-gray-800">R$ {{ number_format($totalAmount, 2, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        </div>

        @if($designatedEntriesTableSpacerHeight > 0)
            @for($i = 0; $i < $designatedEntriesTableSpacerHeight; $i++)
                <div style="height: 1px;"></div>
            @endfor
            <div class="mb-8 bg-gray-100 rounded-xl break-inside-avoid" style="margin-top: -20px;">
                @else
                    <div class="mt-8 mb-8 bg-gray-100 rounded-xl break-inside-avoid">
                        @endif
                        <div class="bg-gray-200 rounded-t-xl px-6 py-4 grid grid-cols-11 gap-x-1">
                            <div class="text-secondary col-span-8 text-lg font-medium">ENTRADAS DESIGNADAS</div>
                            <div class="text-secondary text-right text-lg font-medium">QTD</div>
                            <div class="text-secondary col-span-2 text-right text-lg font-medium">TOTAL</div>
                        </div>
                        <div class="px-6 py-4">
                            <div class="grid grid-cols-11 gap-x-1 gap-y-4">
                                @php $totalDesignatedAmount = 0; @endphp

                                @foreach($reportData->designatedEntriesData as $designatedEntry)
                                    <div class="col-span-8 text-md font-medium">{{ $designatedEntry->name }}</div>
                                    <div class="self-center text-right">{{ $designatedEntry->qtd }}</div>
                                    <div class="col-span-2 self-center text-right">R$ {{ number_format($designatedEntry->total, 2, ',', '.') }}</div>
                                    <div class="col-span-11 border-b border-gray-300 {{ $loop->last ? 'my-2' : '' }}"></div>
                                    @php $totalDesignatedAmount += $designatedEntry->total; @endphp
                                @endforeach

                                <div class="col-span-11 text-right">
                                    <span class="text-lg font-bold text-gray-800">R$ {{ number_format($totalDesignatedAmount, 2, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    </div>
                </div>
            </div>
    </body>
</html>
