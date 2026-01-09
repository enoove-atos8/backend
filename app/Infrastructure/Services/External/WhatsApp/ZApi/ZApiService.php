<?php

namespace Infrastructure\Services\External\WhatsApp\ZApi;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Infrastructure\Services\External\WhatsApp\Interfaces\WhatsAppServiceInterface;

class ZApiService implements WhatsAppServiceInterface
{
    // API Configuration
    private const BASE_URL = 'https://api.z-api.io/';

    private const TIMEOUT = 30;

    // Endpoints
    private const ENDPOINT_SEND_TEXT = 'instances/%s/token/%s/send-text';

    private const ENDPOINT_STATUS = 'instances/%s/token/%s/status';

    // Headers
    private const HEADER_CONTENT_TYPE = 'Content-Type';

    private const HEADER_CLIENT_TOKEN = 'Client-Token';

    private const CONTENT_TYPE_JSON = 'application/json';

    // Response keys
    private const RESPONSE_KEY_MESSAGE_ID = 'messageId';

    private const RESPONSE_KEY_ZAAP_ID = 'zaapId';

    private const RESPONSE_KEY_CONNECTED = 'connected';

    // Request body keys
    private const REQUEST_KEY_PHONE = 'phone';

    private const REQUEST_KEY_MESSAGE = 'message';

    // Status
    private const STATUS_SENT = 'sent';

    private Client $client;

    private string $instanceId;

    private string $token;

    private string $clientToken;

    public function __construct()
    {
        $this->instanceId = config('whatsapp.zapi.instance_id');
        $this->token = config('whatsapp.zapi.token');
        $this->clientToken = config('whatsapp.zapi.client_token');

        $this->client = new Client([
            'base_uri' => self::BASE_URL,
            'timeout' => self::TIMEOUT,
            'headers' => [
                self::HEADER_CONTENT_TYPE => self::CONTENT_TYPE_JSON,
                self::HEADER_CLIENT_TOKEN => $this->clientToken,
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
            $endpoint = sprintf(self::ENDPOINT_SEND_TEXT, $this->instanceId, $this->token);

            $response = $this->client->post($endpoint, [
                'json' => [
                    self::REQUEST_KEY_PHONE => $to,
                    self::REQUEST_KEY_MESSAGE => $message,
                ],
            ]);

            $body = json_decode($response->getBody()->getContents(), true);

            return [
                'success' => true,
                'message_id' => $body[self::RESPONSE_KEY_MESSAGE_ID] ?? $body[self::RESPONSE_KEY_ZAAP_ID] ?? null,
                'status' => self::STATUS_SENT,
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
            $endpoint = sprintf(self::ENDPOINT_STATUS, $this->instanceId, $this->token);

            $response = $this->client->get($endpoint);

            $body = json_decode($response->getBody()->getContents(), true);

            // Z-API retorna boolean que pode ser decodificado como 1/0 ou true/false
            return ! empty($body[self::RESPONSE_KEY_CONNECTED]);
        } catch (GuzzleException $e) {
            return false;
        }
    }

    /**
     * Get instance/session name
     */
    public function getInstanceName(): string
    {
        return $this->instanceId;
    }
}
