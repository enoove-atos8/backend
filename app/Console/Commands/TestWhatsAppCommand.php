<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Infrastructure\Services\External\WhatsApp\Interfaces\WhatsAppServiceInterface;

class TestWhatsAppCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'whatsapp:test {phone : Phone number to send test message (ex: 5581999999999)}';

    /**
     * The console command description.
     */
    protected $description = 'Enviar mensagem de teste via WhatsApp';

    /**
     * Execute the console command.
     */
    public function handle(WhatsAppServiceInterface $whatsAppService): int
    {
        $phone = $this->argument('phone');

        // Adiciona código do Brasil se não tiver
        if (! str_starts_with($phone, '55') && ! str_starts_with($phone, '+55')) {
            $phone = '55'.$phone;
        }

        $this->info('🔄 Testando integração WhatsApp...');
        $this->info('');
        $this->info("Driver: ".config('whatsapp.driver'));
        $this->info("Instância: ".$whatsAppService->getInstanceName());
        $this->info("Destinatário: {$phone}");
        $this->info('');

        // Verificar conexão
        $this->info('🔍 Verificando conexão...');
        if (! $whatsAppService->isConnected()) {
            $this->error('❌ Serviço WhatsApp não está conectado!');

            return Command::FAILURE;
        }
        $this->info('✅ Serviço conectado!');
        $this->info('');

        // Enviar mensagem de teste
        $this->info('📤 Enviando mensagem de teste...');

        try {
            $message = "🎉 Teste de integração WhatsApp via Twilio!\n\n";
            $message .= "Se você recebeu esta mensagem, a integração está funcionando perfeitamente!\n\n";
            $message .= "✅ Driver: ".config('whatsapp.driver')."\n";
            $message .= "✅ Instância: ".$whatsAppService->getInstanceName()."\n";
            $message .= "✅ Data/Hora: ".now()->format('d/m/Y H:i:s');

            $response = $whatsAppService->sendTextMessage($phone, $message);

            $this->info('');
            $this->info('✅ Mensagem enviada com sucesso!');
            $this->info('');
            $this->table(
                ['Campo', 'Valor'],
                [
                    ['Success', $response['success'] ? 'Sim' : 'Não'],
                    ['Message ID', $response['message_id'] ?? 'N/A'],
                    ['Status', $response['status'] ?? 'N/A'],
                ]
            );

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('');
            $this->error('❌ Erro ao enviar mensagem:');
            $this->error($e->getMessage());
            $this->error('');

            return Command::FAILURE;
        }
    }
}
