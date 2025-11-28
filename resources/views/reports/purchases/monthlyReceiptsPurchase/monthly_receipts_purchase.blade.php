@php use Carbon\Carbon; @endphp
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Relatório de Comprovantes de Compras</title>
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
                            <div class="text-lg font-bold text-gray-800">Relatório de Comprovantes de Compras</div>
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

            <div class="bg-gray-200 rounded-xl p-6">
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
                        <div class="text-secondary text-[14px] font-medium tracking-tight mb-2">TOTAL</div>
                        <div class="font-semibold text-gray-800 text-lg">
                            R$ {{ number_format($reportData->generalData->totalAmount, 2, ',', '.') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="page-break"></div>

{{-- COMPROVANTES --}}
@foreach ($receiptPaths as $receiptPath)
    @if (file_exists($receiptPath))
        <div class="image-container">
            <img src="{{ $receiptPath }}" alt="Comprovante">
        </div>
    @endif
@endforeach

</body>
</html>
