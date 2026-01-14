<?php

namespace Infrastructure\Services\External\WhatsApp;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Infrastructure\Services\External\WhatsApp\Interfaces\WhatsAppServiceInterface;

class TwilioService implements WhatsAppServiceInterface
{
    private Client $client;

    private string $accountSid;

    private string $authToken;

    private string $phoneNumber;

    public function __construct()
    {
        $this->accountSid = config('whatsapp.twilio.account_sid');
        $this->authToken = config('whatsapp.twilio.auth_token');
        $this->phoneNumber = config('whatsapp.twilio.phone_number');

        $this->client = new Client([
            'base_uri' => 'https://api.twilio.com/2010-04-01/',
            'timeout' => 30,
            'auth' => [$this->accountSid, $this->authToken],
        ]);
    }

    /**
     * Send a text message via WhatsApp
     *
     * @throws \Exception
     */
    public function sendTextMessage(string $to, string $message): array
    {
        try {
            // Formatar números para o padrão Twilio
            $from = $this->formatTwilioNumber($this->phoneNumber);
            $to = $this->formatTwilioNumber($to);

            $response = $this->client->post("Accounts/{$this->accountSid}/Messages.json", [
                'form_params' => [
                    'From' => $from,
                    'To' => $to,
                    'Body' => $message,
                ],
            ]);

            $body = json_decode($response->getBody()->getContents(), true);

            return [
                'success' => true,
                'message_id' => $body['sid'] ?? null,
                'status' => $body['status'] ?? 'queued',
                'response' => $body,
            ];
        } catch (GuzzleException $e) {
            throw new \Exception('Erro ao enviar mensagem WhatsApp via Twilio: '.$e->getMessage());
        }
    }

    /**
     * Check if the service is connected and ready
     */
    public function isConnected(): bool
    {
        try {
            // Verificar se a conta está acessível
            $response = $this->client->get("Accounts/{$this->accountSid}.json");

            return $response->getStatusCode() === 200;
        } catch (GuzzleException $e) {
            return false;
        }
    }

    /**
     * Get instance/session name
     */
    public function getInstanceName(): string
    {
        return $this->accountSid;
    }

    /**
     * Format phone number to Twilio WhatsApp format
     * Converts: 5581999000918 -> whatsapp:+5581999000918
     */
    private function formatTwilioNumber(string $number): string
    {
        // Remove espaços e caracteres especiais
        $number = preg_replace('/[^0-9+]/', '', $number);

        // Se já tem o prefixo whatsapp:, retornar
        if (str_starts_with($number, 'whatsapp:')) {
            return $number;
        }

        // Se não tem +, adicionar
        if (! str_starts_with($number, '+')) {
            $number = '+'.$number;
        }

        // Adicionar prefixo whatsapp:
        return 'whatsapp:'.$number;
    }
}
