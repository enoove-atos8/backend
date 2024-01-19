<?php

namespace Domain\ConsolidationEntries\Constants;

class ReturnMessages
{
    //Success messages
    public const SUCCESS_ENTRIES_CONSOLIDATED = "Consolidação de entradas realizada com sucesso!";


    // Error messages
    public const ERROR_GET_CONSOLIDATED_ENTRIES = "Não foram encontradas entradas consolidadas!";
    public const ERROR_GET_CONSOLIDATED_ENTRIES_NOT_FOUND = "Não foram encontradas meses consolidados!";
    public const ERROR_CREATE_ENTRIES_CONSOLIDATED_MONTH = "Não é mais possível criar uma entrada em um mês já consolidado!";
    public const ERROR_UPDATE_ENTRIES_CONSOLIDATED = "Ocorreu um erro ao atualizar o status de consolidação das entradas selecionadas!";

    // Info messages


}
