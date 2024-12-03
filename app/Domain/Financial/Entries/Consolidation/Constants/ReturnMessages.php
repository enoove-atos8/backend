<?php

namespace App\Domain\Financial\Entries\Consolidation\Constants;

class ReturnMessages
{
    //Success messages
    public const SUCCESS_ENTRIES_CONSOLIDATED = "Consolidação realizada com sucesso!";


    // Error messages
    public const ERROR_GET_CONSOLIDATED_ENTRIES = "Ainda não existem meses consolidados...";
    public const ERROR_GET_CONSOLIDATED_ENTRIES_NOT_FOUND = "Ainda não existem meses a serem consolidados...";
    public const ERROR_CREATE_ENTRIES_CONSOLIDATED_MONTH = "Não é mais possível criar ou editar uma entrada em um mês já consolidado!";
    public const ERROR_UPDATE_ENTRIES_CONSOLIDATED = "Ocorreu um erro ao atualizar o status de consolidação das entradas selecionadas!";
    public const ERROR_REQUIRED_TWO_MONTHS_CONSOLIDATED = "É necessário ao menos 2 meses consolidados...";
    public const ERROR_NOT_COMPENSATED_ENTRIES_FOUNDED = "Existem entradas que ainda não foram compensadas para os meses selecionados, verifique!";
    public const ERROR_NOT_ALLOW_NEW_ENTRY_WITH_PREVIOUS_MONTHS_NOT_CONSOLIDATE = "Não é possível registrar uma nova entrada quando existem meses anteriores não consolidados, verifique!";

    // Info messages

    public const INFO_ATTEMPT_OF_CONSOLIDATE_CURRENT_MONTH = "Você selecionou mês atual para ser consolidado, tem certeza que deseja prosseguir?";


}
