<?php

namespace Infrastructure\Services\External\WhatsApp;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Infrastructure\Services\External\WhatsApp\Interfaces\WhatsAppServiceInterface;

class MetaCloudApiService implements WhatsAppServiceInterface
{
    private Client $client;

    private string $phoneNumberId;

    private string $accessToken;

    private string $apiVersion;

    public function __construct()
    {
        $this->phoneNumberId = config('whatsapp.meta.phone_number_id');
        $this->accessToken = config('whatsapp.meta.access_token');
        $this->apiVersion = config('whatsapp.meta.api_version', 'v21.0');

        $this->client = new Client([
            'base_uri' => "https://graph.facebook.com/{$this->apiVersion}/",
            'timeout' => 30,
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => "Bearer {$this->accessToken}",
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
            $response = $this->client->post("{$this->phoneNumberId}/messages", [
                'json' => [
                    'messaging_product' => 'whatsapp',
                    'to' => $to,
                    'type' => 'text',
                    'text' => [
                        'body' => $message,
                    ],
                ],
            ]);

            $body = json_decode($response->getBody()->getContents(), true);

            return [
                'success' => true,
                'message_id' => $body['messages'][0]['id'] ?? null,
                'status' => 'sent',
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
            $response = $this->client->get($this->phoneNumberId);

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
        return $this->phoneNumberId;
    }
}
