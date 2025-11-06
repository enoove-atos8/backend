@php
    use Carbon\Carbon;

    // Formatar datas
    $referenceDate = Carbon::createFromFormat('Y-m', $reportData->generalReportData->period);
    $currentMonth = $referenceDate->locale('pt_BR')->isoFormat('MMMM/YYYY');
    $currentMonthShort = $referenceDate->locale('pt_BR')->isoFormat('MMM/YYYY');
    $previousMonth = $referenceDate->copy()->subMonth()->locale('pt_BR')->isoFormat('MMM/YYYY');
@endphp
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Relatório de Saldos Mensais</title>
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
            <!-- Cards do topo: Igreja e Informações do Relatório -->
            <div class="grid grid-cols-2 gap-6 mb-6">
                <!-- Card 1: Informações da Igreja -->
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
                            <div><span class="font-semibold">CNPJ:</span> {{ $reportData->churchData->docNumber }}</div>
                        </div>
                    </div>
                </div>

                <!-- Card 2: Informações do Relatório -->
                <div class="bg-gray-200 rounded-xl p-6">
                    <div class="h-full flex flex-col justify-between font-inter">
                        <div class="mb-4">
                            <div class="text-lg font-bold text-gray-800">Relatório financeiro de saldos gerais</div>
                        </div>
                        <div class="space-y-2 text-sm">
                            <div class="flex items-center gap-2">
                                <span class="text-gray-700 font-semibold">Período:</span>
                                <span class="bg-gray-400 text-white px-3 py-1 rounded-full text-xs font-semibold">
                                    {{ $currentMonth }}
                                </span>
                            </div>
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

            <!-- Tabela de Saldos -->
            <div class="bg-gray-200 rounded-xl p-6 mb-6">
                <div class="font-inter">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">Saldos gerais - {{ $currentMonth }}</h3>

                    <table class="w-full">
                        <thead>
                            <tr class="border-b-2 border-gray-300">
                                <th class="text-left py-3 px-4 font-semibold text-gray-700">Descrição</th>
                                <th class="text-right py-3 px-4 font-semibold text-gray-700">Valor (R$)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="border-b border-gray-300">
                                <td class="py-3 px-4 text-gray-700">Saldo do mês anterior - {{ $previousMonth }}</td>
                                <td class="py-3 px-4 text-right text-gray-700 font-bold">
                                    {{ number_format($reportData->balancesData->previousBalance, 2, ',', '.') }}
                                </td>
                            </tr>
                            <tr class="border-b border-gray-300">
                                <td class="py-3 px-4 text-gray-700 font-semibold">+ Total de Entradas</td>
                                <td class="py-3 px-4 text-right text-gray-700 font-bold">
                                    {{ number_format($reportData->balancesData->totalEntries, 2, ',', '.') }}
                                </td>
                            </tr>
                            <tr class="border-b border-gray-300">
                                <td class="py-3 px-4 text-gray-700 font-semibold">- Total de Saídas</td>
                                <td class="py-3 px-4 text-right text-gray-700 font-bold">
                                    {{ number_format($reportData->balancesData->totalExits, 2, ',', '.') }}
                                </td>
                            </tr>
                            <tr class="border-b-2 border-gray-300">
                                <td class="py-4 px-4 text-gray-800 font-bold text-lg">Saldo atual - {{ $currentMonthShort }}</td>
                                <td class="py-4 px-4 text-right text-gray-800 font-bold text-lg">
                                    {{ number_format($reportData->balancesData->currentBalance, 2, ',', '.') }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Rodapé com data de geração -->
            <div class="text-center text-sm text-gray-600 mt-8">
                <p>Relatório gerado em {{ $reportData->generalReportData->generationDate }}</p>
            </div>
        </div>
    </div>
</div>
</body>
</html>
