<?php

namespace App\Domain\Financial\Entries\Reports\Constants;

class ReturnMessages
{
    // Success messages
    public const SUCCESS_REPORT_SEND_TO_PROCESS = "Relatório enviado para processamento, em alguns instantes ele estará disponível!";

    // Error messages
    public const NO_REPORT_FOUNDED = "Nada por aqui...";
    public const NO_REPORT_REQUEST_FOUNDED = "Não encontramos nenhuma solicitação de geração de relatório...";

    // Info messages
    public const INFO_AMOUNT_BY_ENTRY_TYPE_NO_RECORDS = "Sem informações...";

}
