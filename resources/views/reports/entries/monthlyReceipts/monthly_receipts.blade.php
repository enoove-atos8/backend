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

        .break-inside-avoid {
            break-inside: avoid;
            page-break-inside: avoid;
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
                                <h2 class="text-lg font-bold text-gray-800">Nome da Igreja</h2>
                            </div>
                        </div>
                        <div class="text-sm text-gray-700 space-y-2">
                            <div>Endereço da Igreja</div>
                            <div><span class="font-semibold">Contato:</span> (00) 00000-0000</div>
                            <div><span class="font-semibold">CNPJ:</span> 00.000.000/0000-00</div>
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

@foreach ($links as $link)
    @if (file_exists($link))
        <div class="image-container">
            <img src="{{ $link }}" alt="Comprovante">
        </div>
    @endif
@endforeach
</body>
</html>
