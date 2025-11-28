@php use Carbon\Carbon; @endphp
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Relatório de Compras Mensais</title>
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

        thead {
            display: table-header-group;
        }

        tbody {
            display: table-row-group;
        }

        /* Container da tabela com cantos arredondados para páginas de continuação */
        table {
            border-collapse: separate;
            border-spacing: 0;
        }

        /* Linha de topo arredondada para continuação - todas as células com fundo */
        thead.continuation-header tr.rounded-top-row td {
            background-color: #f3f4f6;
            height: 12px;
            padding: 0;
        }

        /* Canto superior esquerdo arredondado */
        thead.continuation-header tr.rounded-top-row td:first-child {
            border-top-left-radius: 12px;
        }

        /* Canto superior direito arredondado */
        thead.continuation-header tr.rounded-top-row td:last-child {
            border-top-right-radius: 12px;
        }

        /* Evita que o header do grupo fique sozinho no final da página */
        .group-header {
            break-after: avoid-page;
        }

        /* Margem entre grupos */
        .group-container + .group-container {
            margin-top: 1.5rem;
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
                            <div class="text-lg font-bold text-gray-800">Relatório de Compras Mensais</div>
                        </div>
                        <div class="space-y-2 text-sm">
                            <div class="flex items-center gap-2">
                                <span class="text-gray-700 font-semibold">Cartão:</span>
                                @if($reportData->cardData->creditCardBrand)
                                    <span class="bg-gray-400 text-white px-3 py-1 rounded-full text-xs font-semibold">
                                        {{ strtoupper($reportData->cardData->creditCardBrand) }}
                                    </span>
                                @endif
                            </div>
                            <div class="text-gray-700">
                                <span class="font-semibold">Nome: </span>{{ $reportData->cardData->name }}
                            </div>
                            @if($reportData->cardData->cardNumber)
                            <div class="text-gray-700">
                                <span class="font-semibold">Final: </span>**** {{ substr($reportData->cardData->cardNumber, -4) }}
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-gray-200 rounded-xl p-6 mb-6">
                <div class="grid grid-cols-3 gap-6">
                    <div>
                        <div class="text-secondary text-[14px] font-medium tracking-tight mb-2">PERÍODO</div>
                        <div class="font-semibold text-gray-800 text-lg">{{ $reportData->generalData->period }}</div>
                    </div>
                    <div>
                        <div class="text-secondary text-[14px] font-medium tracking-tight mb-2">DATA DE EMISSÃO</div>
                        <div class="font-semibold text-gray-800 text-lg">{{ $reportData->generalData->generationDate }}</div>
                    </div>
                    <div>
                        <div class="text-secondary text-[14px] font-medium tracking-tight mb-2">TOTAL DE COMPRAS</div>
                        <div class="font-semibold text-gray-800 text-lg">
                            R$ {{ number_format($reportData->generalData->totalPurchases, 2, ',', '.') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- COMPRAS AGRUPADAS POR GRUPO (RECEBEDOR) --}}
@foreach($reportData->purchasesByGroup as $group)
<div class="w-full text-left p-0 group-container">
    <div class="bg-card w-full rounded-none bg-transparent shadow-none">
        <div class="w-full">
            <div class="mb-4 bg-gray-100 rounded-xl">
                <div class="bg-gray-200 rounded-t-xl px-6 py-4 group-header">
                    <div class="flex justify-between items-center">
                        <div>
                            <div class="text-lg font-bold text-gray-800">{{ $group->groupName }}</div>
                            <div class="text-sm text-gray-600">{{ $group->quantity }} lançamento(s)</div>
                        </div>
                        <div class="text-right">
                            <div class="text-secondary text-sm">Total</div>
                            <div class="font-bold text-lg text-gray-800">R$ {{ number_format($group->total, 2, ',', '.') }}</div>
                        </div>
                    </div>
                </div>
                <div class="px-6 py-4">
                    <table class="w-full">
                        {{-- Header: será repetido nas páginas de continuação com cantos arredondados --}}
                        <thead class="continuation-header">
                            {{-- Linha que cria os cantos arredondados no topo --}}
                            <tr class="rounded-top-row">
                                <td class="w-24"></td>
                                <td></td>
                                <td class="w-28"></td>
                                <td class="w-20"></td>
                                <td class="w-32"></td>
                            </tr>
                            <tr class="text-sm font-semibold text-gray-600 border-b border-gray-300">
                                <th class="text-left py-2 w-24">DATA</th>
                                <th class="text-left py-2">DESCRIÇÃO</th>
                                <th class="text-center py-2 w-28">PARCELA</th>
                                <th class="text-center py-2 w-20">ANDAMENTO</th>
                                <th class="text-right py-2 w-32">TOTAL</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($group->purchases as $purchase)
                                <tr class="border-b border-gray-200">
                                    <td class="py-3 text-sm align-top">
                                        {{ $purchase->date ? Carbon::parse($purchase->date)->format('d/m/Y') : '-' }}
                                    </td>
                                    <td class="py-3 align-top">
                                        <div class="text-sm font-medium text-gray-800">{{ $purchase->establishmentName ?? '-' }}</div>
                                        <div class="text-xs text-gray-500">{{ ucfirst(strtolower($purchase->purchaseDescription ?? '-')) }}</div>
                                    </td>
                                    <td class="py-3 text-center text-sm align-top">
                                        R$ {{ number_format($purchase->installmentAmount ?? $purchase->amount, 2, ',', '.') }}
                                    </td>
                                    <td class="py-3 text-center text-sm align-top">
                                        {{ $purchase->currentInstallment ?? 1 }}/{{ $purchase->installments ?? 1 }}
                                    </td>
                                    <td class="py-3 text-right text-sm font-medium align-top">
                                        R$ {{ number_format($purchase->amount, 2, ',', '.') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@endforeach

</body>
</html>
