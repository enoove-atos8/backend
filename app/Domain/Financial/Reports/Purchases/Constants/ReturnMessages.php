<?php

namespace App\Domain\Financial\Reports\Purchases\Constants;

class ReturnMessages
{
    // Success messages
    public const SUCCESS_REPORT_SEND_TO_PROCESS = "Relatório enviado para processamento, em alguns instantes ele estará disponível!";

    // Error messages
    public const NO_REPORT_FOUNDED = "Nada por aqui...";
    public const NO_REPORT_REQUEST_FOUNDED = "Não encontramos nenhuma solicitação de geração de relatório...";
    public const NO_CARDS_FOUND = "Não existem cartões de crédito cadastrados, cadastre um agora!";
    public const NO_PURCHASES_FOUND = "Não existem compras cadastradas para este período!";
    public const ERROR_CARDS_NOT_FOUND = "Não encontramos nenhum cartão de crédito cadastrado!";

    // Info messages
    public const INFO_AMOUNT_BY_PURCHASE_NO_RECORDS = "Sem informações...";

}
