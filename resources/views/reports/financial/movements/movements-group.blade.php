@php use Carbon\Carbon; @endphp
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Relatório de Movimentações do Grupo</title>
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
            color-adjust: exact !important;
        }

        body {
            margin: 0;
            padding: 0;
        }

        @media print {
            .page-container {
                page-break-after: always !important;
                break-after: page !important;
                display: block !important;
            }

            .page-container:last-child {
                page-break-after: avoid !important;
                break-after: avoid !important;
            }

            .movement-table {
                page-break-inside: avoid !important;
                break-inside: avoid !important;
            }

            .movement-row {
                page-break-inside: avoid !important;
                break-inside: avoid !important;
            }
        }

        /* Fallback para quando não está em print mode */
        .page-container {
            page-break-after: always;
            break-after: page;
            display: block;
        }

        .page-container:last-child {
            page-break-after: avoid;
            break-after: avoid;
        }
    </style>
</head>
<body>
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
                                <h2 class="text-lg font-bold text-gray-800">{{ $church?->name ?? 'Igreja Local' }}</h2>
                            </div>
                        </div>
                        <div class="text-sm text-gray-700 space-y-2">
                            <div>{{ $church?->address ?? 'Endereço da Igreja' }}</div>
                            <div>
                                <span class="font-semibold">Contato:</span>
                                @if($church && $church->cellPhone)
                                    @php
                                        $phone = preg_replace('/\D/', '', $church->cellPhone);
                                        if (strlen($phone) === 11) {
                                            $maskedPhone = '(' . substr($phone, 0, 2) . ') ' . substr($phone, 2, 5) . '-' . substr($phone, 7);
                                        } elseif (strlen($phone) === 10) {
                                            $maskedPhone = '(' . substr($phone, 0, 2) . ') ' . substr($phone, 2, 4) . '-' . substr($phone, 6);
                                        } else {
                                            $maskedPhone = $church->cellPhone;
                                        }
                                    @endphp
                                    {{ $maskedPhone }}
                                @else
                                    (00) 00000-0000
                                @endif
                            </div>
                            <div>
                                <span class="font-semibold">CNPJ:</span>
                                @if($church && $church->docNumber)
                                    @php
                                        $cnpj = preg_replace('/\D/', '', $church->docNumber);
                                        if (strlen($cnpj) === 14) {
                                            $maskedCnpj = substr($cnpj, 0, 2) . '.' . substr($cnpj, 2, 3) . '.' . substr($cnpj, 5, 3) . '/' . substr($cnpj, 8, 4) . '-' . substr($cnpj, 12, 2);
                                        } else {
                                            $maskedCnpj = $church->docNumber;
                                        }
                                    @endphp
                                    {{ $maskedCnpj }}
                                @else
                                    00.000.000/0000-00
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-200 rounded-xl p-6">
                    <div class="h-full flex flex-col justify-between font-inter">
                        <div class="mb-4">
                            <div class="text-lg font-bold text-gray-800">Relatório de Movimentações do Grupo</div>
                        </div>
                        <div class="space-y-2 text-sm">
                            <div class="flex items-center gap-2">
                                <span class="text-gray-700 font-semibold">Grupo:</span>
                                <span class="bg-gray-400 text-white px-3 py-1 rounded-full text-xs font-semibold">{{ $groupName }}</span>
                            </div>
                            <div class="text-gray-700">
                                <span class="font-semibold">Período: </span>
                                @if($dates === 'all')
                                    Todos os períodos
                                @else
                                    @php
                                        $dateArray = explode(',', $dates);
                                    @endphp
                                    @foreach($dateArray as $date)
                                        @php
                                            $carbonDate = \Carbon\Carbon::createFromFormat('Y-m', trim($date));
                                        @endphp
                                        <span class="bg-gray-400 text-white px-3 py-1 rounded-full text-xs font-semibold">{{ $carbonDate->translatedFormat('M/Y') }}</span>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @php
                $totalEntradas = 0;
                $totalSaidas = 0;
                $saldoFinal = 0;

                foreach($data as $movement) {
                    $movementType = is_array($movement) ? $movement['type'] : $movement->type;
                    $movementAmount = is_array($movement) ? $movement['amount'] : $movement->amount;
                    $movementBalance = is_array($movement) ? $movement['balance'] : $movement->balance;

                    if($movementType === 'entry') {
                        $totalEntradas += $movementAmount;
                    } else {
                        $totalSaidas += $movementAmount;
                    }
                    $saldoFinal = $movementBalance;
                }
            @endphp

            <div class="bg-gray-200 rounded-xl p-6">
                <div class="grid grid-cols-3 gap-6">
                    <div>
                        <div class="text-secondary text-[14px] font-medium tracking-tight mb-2">TOTAL DE ENTRADAS</div>
                        <div class="font-semibold text-green-600 text-lg">
                            R$ {{ number_format($totalEntradas, 2, ',', '.') }}
                        </div>
                    </div>
                    <div>
                        <div class="text-secondary text-[14px] font-medium tracking-tight mb-2">TOTAL DE SAÍDAS</div>
                        <div class="font-semibold text-red-600 text-lg">
                            R$ {{ number_format($totalSaidas, 2, ',', '.') }}
                        </div>
                    </div>
                    <div>
                        <div class="text-secondary text-[14px] font-medium tracking-tight mb-2">SALDO</div>
                        <div class="font-semibold text-gray-800 text-lg">
                            R$ {{ number_format($saldoFinal, 2, ',', '.') }}
                        </div>
                    </div>
                </div>
            </div>

        </div>

        @php
            // Calcular paginação: primeira página 13 linhas, demais páginas 20 linhas
            $totalLines = count($data);
            $allPages = [];
            $currentIndex = 0;

            // Primeira página: 13 linhas
            if ($totalLines > 0) {
                $firstPageLines = min(13, $totalLines);
                $allPages[] = array_slice($data, 0, $firstPageLines);
                $currentIndex = $firstPageLines;
            }

            // Páginas seguintes: 20 linhas cada
            while ($currentIndex < $totalLines) {
                $remainingLines = $totalLines - $currentIndex;
                $linesToTake = min(20, $remainingLines);
                $allPages[] = array_slice($data, $currentIndex, $linesToTake);
                $currentIndex += $linesToTake;
            }
        @endphp

        {{-- Renderizar cada página --}}
        @foreach($allPages as $pageIndex => $pageData)
            @if(count($pageData) > 0)

                <div class="page-container">
                <div class="movement-table mt-8 mb-8 bg-gray-100 rounded-xl w-full">
                    <div class="bg-gray-200 rounded-t-xl px-6 py-4 grid grid-cols-12 gap-x-3">
                        <div class="text-secondary col-span-1 text-center text-sm font-medium">TIPO</div>
                        <div class="text-secondary col-span-2 text-sm font-medium">DATA</div>
                        <div class="text-secondary col-span-2 text-sm font-medium">TRANSAÇÃO</div>
                        <div class="text-secondary col-span-7 text-right text-sm font-medium">VALOR</div>
                    </div>
                    <div class="px-6 py-4">
                        <div class="grid grid-cols-12 gap-x-3 gap-y-3">
                            @foreach($pageData as $movement)
                                @php
                                    $movementDate = is_array($movement) ? ($movement['movementDate'] ?? $movement['movement_date'] ?? null) : $movement->movementDate;
                                    $movementType = is_array($movement) ? $movement['type'] : $movement->type;
                                    $transactionType = is_array($movement) ? ($movement['transactionType'] ?? $movement['transaction_type'] ?? null) : ($movement->transactionType ?? null);
                                    $movementAmount = is_array($movement) ? ($movement['amount'] ?? 0) : $movement->amount;
                                @endphp

                                <div class="movement-row col-span-12 grid grid-cols-12 gap-x-3">
                                    <div class="col-span-1 text-center text-sm">
                                        @if($movementType === 'entry')
                                            <svg class="w-5 h-5 inline-block text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v3.586L7.707 9.293a1 1 0 00-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 10.586V7z" clip-rule="evenodd"/>
                                            </svg>
                                        @else
                                            <svg class="w-5 h-5 inline-block text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v3.586l-1.293-1.293a1 1 0 00-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 10.586V7z" clip-rule="evenodd" transform="rotate(180 10 10)"/>
                                            </svg>
                                        @endif
                                    </div>
                                    <div class="col-span-2 text-sm">
                                        {{ isset($movementDate) ? Carbon::parse($movementDate)->format('d/m/Y') : '-' }}
                                    </div>
                                    <div class="col-span-2 text-sm">
                                        @if($transactionType === 'pix')
                                            <span class="bg-yellow-200 text-yellow-800 px-2 py-1 rounded text-xs font-bold">PIX</span>
                                        @elseif($transactionType === 'cash')
                                            <span class="bg-red-200 text-red-800 px-2 py-1 rounded text-xs font-bold">DINHEIRO</span>
                                        @else
                                            <span class="text-gray-500 text-xs">-</span>
                                        @endif
                                    </div>
                                    <div class="col-span-7 text-right text-sm {{ $movementType === 'entry' ? 'text-green-600' : 'text-red-600' }} font-semibold">
                                        R$ {{ number_format($movementAmount, 2, ',', '.') }}
                                    </div>
                                </div>
                                <div class="col-span-12 border-b border-gray-300"></div>
                            @endforeach

                            {{-- Saldo final na última página --}}
                            @if($pageIndex === count($allPages) - 1)
                                <div class="col-span-5 text-right text-md font-bold text-gray-800 mt-4">SALDO:</div>
                                <div class="col-span-7 text-right text-md font-bold text-gray-800 mt-4">
                                    R$ {{ number_format($saldoFinal, 2, ',', '.') }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                </div>

            @endif
        @endforeach

</div>
</body>
</html>
