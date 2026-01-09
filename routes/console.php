<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Limpa registros do Telescope com mais de 24 horas
Schedule::command('telescope:prune --hours=24')->daily();

// Marca solicitações de verba como vencidas (executa à meia-noite)
Schedule::command('amount-requests:mark-overdue')->dailyAt('00:00');

// Envia lembretes de comprovação via WhatsApp (executa às 9h)
Schedule::command('whatsapp:send-proof-reminders')->dailyAt('09:00');
