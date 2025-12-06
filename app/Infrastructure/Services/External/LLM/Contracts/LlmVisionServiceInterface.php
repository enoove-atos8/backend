<?php

namespace App\Infrastructure\Services\External\LLM\Contracts;

interface LlmVisionServiceInterface
{
    /**
     * Retorna o nome do provedor de LLM
     */
    public function getProviderName(): string;

    /**
     * Processa uma imagem com um prompt e retorna a resposta
     *
     * @param string $imagePath Caminho absoluto para a imagem
     * @param string $prompt Prompt para processar a imagem
     * @return string Resposta do LLM
     */
    public function processImage(string $imagePath, string $prompt): string;

    /**
     * Processa uma imagem com um prompt e retorna JSON estruturado
     *
     * @param string $imagePath Caminho absoluto para a imagem
     * @param string $prompt Prompt para processar a imagem
     * @return array<string, mixed> Resposta do LLM parseada como array
     */
    public function processImageAsJson(string $imagePath, string $prompt): array;
}
