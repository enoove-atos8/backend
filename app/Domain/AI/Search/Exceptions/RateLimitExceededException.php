<?php

namespace App\Domain\AI\Search\Exceptions;

use RuntimeException;

class RateLimitExceededException extends RuntimeException
{
    public function __construct(string $message = 'Limite de requisições da IA excedido. Tente novamente mais tarde.')
    {
        parent::__construct($message);
    }
}
