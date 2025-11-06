<?php

namespace App\Domain\Financial\AccountsAndCards\Accounts\Constants\Files;

class ReturnMessages
{
    // Success messages

    public const FILE_PUT_TO_PROCESS = 'Arquivo enviado para processamento.';

    public const FILE_CREATED = 'Arquivo criado com sucesso.';

    // Error messages

    public const REPROCESS_ONLY_LAST_MONTH = 'Não é possível reprocessar o extrato de %s. Apenas o último mês processado (%s) pode ser reprocessado.';
}
