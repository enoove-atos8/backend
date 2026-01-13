@php use Carbon\Carbon; @endphp
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Histórico de Dízimos - {{ $groupName }}</title>
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

        .member-row {
            page-break-inside: avoid;
            break-inside: avoid;
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
                                <h2 class="text-lg font-bold text-gray-800">{{ $church->name ?? 'Igreja' }}</h2>
                            </div>
                        </div>
                        <div class="text-sm text-gray-700 space-y-2">
                            @if(isset($church->address))
                                <div>{{ $church->address }}</div>
                            @endif
                            @if(isset($church->cellPhone))
                                <div><span class="font-semibold">Contato:</span> {{ $church->cellPhone }}</div>
                            @endif
                            @if(isset($church->docNumber))
                                <div><span class="font-semibold">CNPJ:</span> {{ $church->docNumber }}</div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="bg-gray-200 rounded-xl p-6">
                    <div class="h-full flex flex-col justify-between font-inter">
                        <div class="mb-4">
                            <div class="text-lg font-bold text-gray-800">Histórico de Devolução de Dízimos</div>
                        </div>
                        <div class="space-y-2 text-sm">
                            @if(isset($leaderName))
                            <div class="flex items-center gap-2">
                                <span class="text-gray-700 font-semibold">Líder:</span>
                                <span class="text-gray-800">{{ $leaderName }}</span>
                            </div>
                            @endif
                            <div class="flex items-center gap-2">
                                <span class="text-gray-700 font-semibold">Grupo:</span>
                                <span class="bg-gray-400 text-white px-3 py-1 rounded-full text-xs font-semibold">
                                    {{ $groupName }}
                                </span>
                            </div>
                            <div class="text-gray-700">
                                <span class="font-semibold">Total de Membros: </span>{{ $totalMembers }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-gray-200 rounded-xl p-6">
                <div class="grid grid-cols-3 gap-6">
                    <div>
                        <div class="text-secondary text-[14px] font-medium tracking-tight mb-2">DATA DE EMISSÃO</div>
                        <div class="font-semibold text-gray-800 text-lg">{{ date('d/m/Y') }}</div>
                    </div>
                    <div>
                        <div class="text-secondary text-[14px] font-medium tracking-tight mb-2">PERÍODO</div>
                        <div class="font-semibold text-gray-800 text-lg">Últimos 6 meses</div>
                    </div>
                    <div>
                        <div class="text-secondary text-[14px] font-medium tracking-tight mb-2">MEMBROS</div>
                        <div class="font-semibold text-gray-800 text-lg">{{ $totalMembers }}</div>
                    </div>
                </div>
            </div>

        </div>

        @php
            // Dividir dados em páginas: primeira página 9 membros, demais 15
            $firstPageMembers = array_slice($data, 0, 9);
            $remainingMembers = array_slice($data, 9);
            $otherPagesMembers = array_chunk($remainingMembers, 15);
            $allPages = array_merge([$firstPageMembers], $otherPagesMembers);
        @endphp

        @foreach($allPages as $pageIndex => $pageMembers)
            @if($pageIndex > 0)
                <div class="page-break"></div>
            @endif

            <div class="mt-8 mb-8 bg-gray-100 rounded-xl overflow-hidden">
                <div class="bg-gray-200 rounded-t-xl px-6 py-4">
                    <div class="grid gap-x-2" style="grid-template-columns: 2fr repeat(6, 1fr);">
                        <div class="text-secondary text-sm font-medium">DIZIMISTA</div>
                        @foreach($months as $month)
                            <div class="text-secondary text-center text-sm font-medium">{{ $month['label'] }}</div>
                        @endforeach
                    </div>
                </div>
                <div class="px-6 py-4">
                    <div class="space-y-1">
                        @foreach($pageMembers as $index => $member)
                            @php
                                $globalIndex = ($pageIndex === 0) ? $index : (9 + array_sum(array_map('count', array_slice($otherPagesMembers, 0, $pageIndex - 1))) + $index);
                            @endphp
                            <div class="member-row grid gap-x-2 items-center {{ $globalIndex % 2 === 0 ? 'bg-white' : 'bg-gray-50' }} rounded-lg px-3" style="grid-template-columns: 2fr repeat(6, 1fr); height: 60px;">
                                <div class="text-sm font-medium text-gray-800">
                                    {{ $member['fullName'] ?? 'Nome não informado' }}
                                </div>
                                @foreach($months as $month)
                                    @php
                                        $hasDevolution = $member['titheHistory']['history'][$month['key']] ?? false;
                                        $isDependent = $member['titheHistory']['isDependent'] ?? false;
                                    @endphp
                                    <div class="text-center flex items-center justify-center">
                                        @if($hasDevolution)
                                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                        @elseif($isDependent)
                                            <span class="text-blue-600 text-xs font-semibold">DEP</span>
                                        @else
                                            <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endforeach

    </div>
</div>
</body>
</html>
