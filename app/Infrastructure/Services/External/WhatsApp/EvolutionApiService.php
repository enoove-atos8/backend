<?php

namespace Infrastructure\Services\External\WhatsApp;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Infrastructure\Services\External\WhatsApp\Interfaces\WhatsAppServiceInterface;

class EvolutionApiService implements WhatsAppServiceInterface
{
    private Client $client;

    private string $baseUrl;

    private string $apiKey;

    private string $instanceName;

    public function __construct()
    {
        $this->baseUrl = config('whatsapp.evolution.base_url');
        $this->apiKey = config('whatsapp.evolution.api_key');
        $this->instanceName = config('whatsapp.evolution.instance_name');

        $this->client = new Client([
            'base_uri' => $this->baseUrl,
            'timeout' => 30,
            'headers' => [
                'Content-Type' => 'application/json',
                'apikey' => $this->apiKey,
            ],
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
            $response = $this->client->post("/message/sendText/{$this->instanceName}", [
                'json' => [
                    'number' => $to,
                    'text' => $message,
                ],
            ]);

            $body = json_decode($response->getBody()->getContents(), true);

            return [
                'success' => true,
                'message_id' => $body['key']['id'] ?? null,
                'status' => $body['status'] ?? 'sent',
                'response' => $body,
            ];
        } catch (GuzzleException $e) {
            throw new \Exception('Erro ao enviar mensagem WhatsApp: '.$e->getMessage());
        }
    }

    /**
     * Check if the service is connected and ready
     */
    public function isConnected(): bool
    {
        try {
            $response = $this->client->get("/instance/connectionState/{$this->instanceName}");
            $body = json_decode($response->getBody()->getContents(), true);

            return ($body['state'] ?? '') === 'open';
        } catch (GuzzleException $e) {
            return false;
        }
    }

    /**
     * Get instance/session name
     */
    public function getInstanceName(): string
    {
        return $this->instanceName;
    }
}
