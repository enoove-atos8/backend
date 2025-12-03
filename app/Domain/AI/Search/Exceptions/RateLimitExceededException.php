<?php

namespace App\Domain\AI\Search\Exceptions;

use RuntimeException;

class RateLimitExceededException extends RuntimeException
{
    public function __construct(string $retryAfter = 'alguns minutos')
    {
        $message = "Limite de requisições da IA excedido. Tente novamente em {$retryAfter}.";
        parent::__construct($message);
    }
}
