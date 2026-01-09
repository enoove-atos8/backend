<?php

namespace Infrastructure\Services\External\WhatsApp\Interfaces;

interface WhatsAppServiceInterface
{
    /**
     * Send a text message via WhatsApp
     *
     * @param  string  $to  Phone number in format: 5581999000918
     * @param  string  $message  Message content
     * @return array Response with status and message_id
     *
     * @throws \Exception
     */
    public function sendTextMessage(string $to, string $message): array;

    /**
     * Check if the service is connected and ready
     */
    public function isConnected(): bool;

    /**
     * Get instance/session name
     */
    public function getInstanceName(): string;
}
