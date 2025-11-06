<?php

namespace App\Domain\Financial\AccountsAndCards\Accounts\Constants\Movements;

class ReturnMessages
{
    // Success messages

    public const FILE_PUT_TO_PROCESS = 'Arquivo enviado para processamento.';

    // Error messages

    public const INSERT_BULK_MOVEMENTS_ERROR = 'Ocorreu um erro ao tentar inserir as movimentações do extrado na base de dados';

    public const SEQUENTIAL_PROCESSING_ERROR = 'Não é possível processar o extrato de %s. O último mês processado foi %s. Você deve processar o mês %s em sequência ou reprocessar %s.';
}
