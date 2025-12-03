<?php

namespace App\Domain\AI\Search\Interfaces;

interface LlmServiceInterface
{
    /**
     * Gera SQL a partir de uma pergunta em linguagem natural
     */
    public function generateSql(string $question, string $schema): string;

    /**
     * Formata a resposta dos dados para exibição ao usuário
     *
     * @param  array<mixed>  $data
     * @return array{title: string, description: string, suggested_followup: string}
     */
    public function formatResponse(string $question, array $data): array;
}
