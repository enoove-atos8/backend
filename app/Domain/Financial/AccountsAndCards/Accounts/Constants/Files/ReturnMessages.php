<?php

namespace App\Domain\Financial\AccountsAndCards\Accounts\Constants\Files;

class ReturnMessages
{
    // Success messages

    public const FILE_PUT_TO_PROCESS = 'Arquivo enviado para processamento.';

    public const FILE_CREATED = 'Arquivo criado com sucesso.';

    public const FUTURE_DATA_DELETED = 'Dados dos meses posteriores foram apagados e precisam ser reprocessados: %s';

    // Error messages

    public const REPROCESS_ONLY_LAST_MONTH = 'Não é possível reprocessar o extrato de %s. Apenas o último mês processado (%s) pode ser reprocessado.';

    public const FUTURE_MONTHS_EXIST = 'Existem meses posteriores já processados (%s). Ao processar %s, os dados desses meses serão apagados e precisarão ser reprocessados em ordem.';

    public const INITIAL_BALANCE_REQUIRED = 'É necessário informar o saldo inicial de %s para processar o extrato de %s.';
}
