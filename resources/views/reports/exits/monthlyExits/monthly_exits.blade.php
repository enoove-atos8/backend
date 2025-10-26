@php use Carbon\Carbon; @endphp
    <!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Relatório de Saídas Mensais</title>
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
                            <div><span class="font-semibold">CNPJ:</span>{{ $reportData->churchData->docNumber }}</div>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-200 rounded-xl p-6">
                    <div class="h-full flex flex-col justify-between font-inter">
                        <div class="mb-4">
                            <div class="text-lg font-bold text-gray-800">Relatório financeiro de saídas mensal</div>
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
                        <div class="text-secondary text-[14px] font-medium tracking-tight mb-2">TOTAL DE SAÍDAS</div>
                        <div class="font-semibold text-gray-800 text-lg">
                            R$ {{ number_format($reportData->generalReportData->totalExits, 2, ',', '.') }}</div>
                    </div>
                    <div>
                        <div class="text-secondary text-[14px] font-medium tracking-tight mb-2">LANÇAMENTOS</div>
                        <div
                            class="font-semibold text-gray-800 text-lg">{{ $reportData->generalReportData->quantity }}</div>
                    </div>
                </div>
            </div>

        </div>
        <div class="mt-8 mb-8 bg-gray-100 rounded-xl">
            <div class="bg-gray-200 rounded-t-xl px-6 py-4 grid grid-cols-11 gap-x-1">
                <div class="text-secondary col-span-7 text-lg font-medium">SAÍDAS</div>
                <div class="text-secondary text-right text-lg font-medium">QTD</div>
                <div class="text-secondary col-span-3 text-right text-lg font-medium">TOTAL</div>
            </div>
            <div class="px-6 py-4">
                <div class="grid grid-cols-11 gap-x-1 gap-y-4">
                    @php
                        $exitsMap = [
                            'payments' => 'Pagamentos',
                            'contributions' => 'Contribuições',
                            'ministerialTransfer' => 'Repasse Ministerial',
                            'transfer' => 'Transferências'
                        ];
                        $totalAmount = 0;
                    @endphp

                    @foreach($exitsMap as $key => $label)
                        @if(isset($reportData->exitsData->$key))
                            <div class="col-span-7 text-md font-medium">{{ $label }}</div>
                            <div class="self-center text-right">{{ $reportData->exitsData->$key->qtd }}</div>
                            <div class="col-span-3 self-center text-right">R$ {{ number_format($reportData->exitsData->$key->total, 2, ',', '.') }}</div>
                            <div class="col-span-11 border-b border-gray-300"></div>
                            @php $totalAmount += $reportData->exitsData->$key->total; @endphp
                        @endif
                    @endforeach

                    @if(isset($reportData->exitsData->totalAccountsTransfer) && $reportData->exitsData->totalAccountsTransfer > 0)
                        <div class="col-span-7 text-md font-medium">Transferências entre contas</div>
                        <div class="self-center text-right"></div>
                        <div class="col-span-3 self-center text-right">R$ {{ number_format($reportData->exitsData->totalAccountsTransfer, 2, ',', '.') }}</div>
                        <div class="col-span-11 border-b border-gray-300"></div>
                    @endif

                    <div class="col-span-11 text-right">
                        <span class="text-lg font-bold text-gray-800">R$ {{ number_format($totalAmount, 2, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="page-break"></div>

{{-- PAGAMENTOS --}}
@if(count($reportData->paymentsData) > 0)
<div class="w-full text-left p-0">
    <div class="bg-card w-full rounded-none bg-transparent shadow-none">
        <div class="w-full">
            <div class="mb-8 bg-gray-100 rounded-xl">
                <div class="bg-gray-200 rounded-t-xl px-6 py-4 grid grid-cols-11 gap-x-1">
                    <div class="text-secondary col-span-7 text-lg font-medium">PAGAMENTOS</div>
                    <div class="text-secondary text-right text-lg font-medium">QTD</div>
                    <div class="text-secondary col-span-3 text-right text-lg font-medium">TOTAL</div>
                </div>
                <div class="px-6 py-4">
                    <div class="grid grid-cols-11 gap-x-1 gap-y-4">
                        @php $totalPaymentsAmount = 0; @endphp

                        @foreach($reportData->paymentsData as $category)
                            {{-- Category Header --}}
                            <div class="col-span-7 text-md font-bold text-gray-800">{{ $category->categoryName }}</div>
                            <div class="self-center text-right font-semibold">{{ $category->categoryQtd }}</div>
                            <div class="col-span-3 self-center text-right font-semibold">R$ {{ number_format($category->categoryTotal, 2, ',', '.') }}</div>

                            {{-- Category Items --}}
                            @foreach($category->items as $item)
                                <div class="col-span-7 text-sm pl-4">{{ $item->itemName }}</div>
                                <div class="self-center text-right text-sm">{{ $item->qtd }}</div>
                                <div class="col-span-3 self-center text-right text-sm">R$ {{ number_format($item->total, 2, ',', '.') }}</div>
                            @endforeach

                            <div class="col-span-11 border-b border-gray-300"></div>
                            @php $totalPaymentsAmount += $category->categoryTotal; @endphp
                        @endforeach

                        <div class="col-span-11 text-right">
                            <span class="text-lg font-bold text-gray-800">R$ {{ number_format($totalPaymentsAmount, 2, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="page-break"></div>
@endif

{{-- TRANSFERÊNCIAS --}}
@if(count($reportData->transferData) > 0)
<div class="w-full text-left p-0">
    <div class="bg-card w-full rounded-none bg-transparent shadow-none">
        <div class="w-full">
            <div class="mb-8 bg-gray-100 rounded-xl">
                <div class="bg-gray-200 rounded-t-xl px-6 py-4 grid grid-cols-11 gap-x-1">
                    <div class="text-secondary col-span-7 text-lg font-medium">TRANSFERÊNCIAS</div>
                    <div class="text-secondary text-right text-lg font-medium">QTD</div>
                    <div class="text-secondary col-span-3 text-right text-lg font-medium">TOTAL</div>
                </div>
                <div class="px-6 py-4">
                    <div class="grid grid-cols-11 gap-x-1 gap-y-4">
                        @php $totalTransferAmount = 0; @endphp

                        @foreach($reportData->transferData as $division)
                            {{-- Division Header --}}
                            <div class="col-span-7 text-md font-bold text-gray-800">{{ $division->divisionName }}</div>
                            <div class="self-center text-right font-semibold">{{ $division->divisionQtd }}</div>
                            <div class="col-span-3 self-center text-right font-semibold">R$ {{ number_format($division->divisionTotal, 2, ',', '.') }}</div>

                            {{-- Division Groups --}}
                            @foreach($division->groups as $group)
                                <div class="col-span-7 text-sm pl-4">{{ $group->groupName }}</div>
                                <div class="self-center text-right text-sm">{{ $group->qtd }}</div>
                                <div class="col-span-3 self-center text-right text-sm">R$ {{ number_format($group->total, 2, ',', '.') }}</div>
                            @endforeach

                            <div class="col-span-11 border-b border-gray-300"></div>
                            @php $totalTransferAmount += $division->divisionTotal; @endphp
                        @endforeach

                        <div class="col-span-11 text-right">
                            <span class="text-lg font-bold text-gray-800">R$ {{ number_format($totalTransferAmount, 2, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="page-break"></div>
@endif

{{-- REPASSE MINISTERIAL --}}
@if(count($reportData->ministerialTransferData) > 0)
<div class="w-full text-left p-0">
    <div class="bg-card w-full rounded-none bg-transparent shadow-none">
        <div class="w-full">
            <div class="mb-8 bg-gray-100 rounded-xl">
                <div class="bg-gray-200 rounded-t-xl px-6 py-4 grid grid-cols-11 gap-x-1">
                    <div class="text-secondary col-span-7 text-lg font-medium">REPASSE MINISTERIAL</div>
                    <div class="text-secondary text-right text-lg font-medium">QTD</div>
                    <div class="text-secondary col-span-3 text-right text-lg font-medium">TOTAL</div>
                </div>
                <div class="px-6 py-4">
                    <div class="grid grid-cols-11 gap-x-1 gap-y-4">
                        @php $totalMinisterialAmount = 0; @endphp

                        @foreach($reportData->ministerialTransferData as $ministerial)
                            <div class="col-span-7 text-md font-medium">{{ $ministerial->name }}</div>
                            <div class="self-center text-right">{{ $ministerial->qtd }}</div>
                            <div class="col-span-3 self-center text-right">R$ {{ number_format($ministerial->total, 2, ',', '.') }}</div>
                            <div class="col-span-11 border-b border-gray-300"></div>
                            @php $totalMinisterialAmount += $ministerial->total; @endphp
                        @endforeach

                        <div class="col-span-11 text-right">
                            <span class="text-lg font-bold text-gray-800">R$ {{ number_format($totalMinisterialAmount, 2, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="page-break"></div>
@endif

{{-- CONTRIBUIÇÕES --}}
@if(count($reportData->contributionsData) > 0)
<div class="w-full text-left p-0">
    <div class="bg-card w-full rounded-none bg-transparent shadow-none">
        <div class="w-full">
            <div class="mb-8 bg-gray-100 rounded-xl">
                <div class="bg-gray-200 rounded-t-xl px-6 py-4 grid grid-cols-11 gap-x-1">
                    <div class="text-secondary col-span-7 text-lg font-medium">CONTRIBUIÇÕES</div>
                    <div class="text-secondary text-right text-lg font-medium">QTD</div>
                    <div class="text-secondary col-span-3 text-right text-lg font-medium">TOTAL</div>
                </div>
                <div class="px-6 py-4">
                    <div class="grid grid-cols-11 gap-x-1 gap-y-4">
                        @php $totalContributionsAmount = 0; @endphp

                        @foreach($reportData->contributionsData as $contribution)
                            <div class="col-span-7 text-md font-medium">{{ $contribution->name }}</div>
                            <div class="self-center text-right">{{ $contribution->qtd }}</div>
                            <div class="col-span-3 self-center text-right">R$ {{ number_format($contribution->total, 2, ',', '.') }}</div>
                            <div class="col-span-11 border-b border-gray-300"></div>
                            @php $totalContributionsAmount += $contribution->total; @endphp
                        @endforeach

                        <div class="col-span-11 text-right">
                            <span class="text-lg font-bold text-gray-800">R$ {{ number_format($totalContributionsAmount, 2, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
</body>
</html>
