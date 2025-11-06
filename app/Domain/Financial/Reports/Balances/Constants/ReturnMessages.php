<?php

namespace App\Domain\Financial\Reports\Balances\Constants;

class ReturnMessages
{
    // Success messages
    public const SUCCESS_REPORT_SEND_TO_PROCESS = 'Relatório enviado para processamento, em alguns instantes ele estará disponível!';

    // Error messages
    public const NO_REPORT_FOUNDED = 'Nada por aqui...';

    public const NO_REPORT_REQUEST_FOUNDED = 'Não encontramos nenhuma solicitação de geração de relatório...';

    public const ERROR_ACCOUNTS_NOT_FOUND_AND_EXTRACT_NOT_FOUND = 'Não existem contas bancárias cadastradas, cadastre uma agora e anexo um arquivo de extrato';

    public const EXTRACT_NOT_FOUND = 'Não existe nenhum extrato bancário cadastrado para este mês!';

    public const EXTRACT_NOT_PROCESSED = 'O extrato bancário ainda não foi processado. Inicie ou aguarde o processamento antes de gerar o relatório de saldos!';

    // Info messages
    public const INFO_NO_BALANCES_RECORDS = 'Sem informações de saldos...';
}
