<?php

namespace App\Domain\Entries\Constants;

class ReturnMessages
{
    // Success messages
    public const SUCCESS_ENTRY_REGISTERED = "Entrada registrada com sucesso!";

    // Error messages
    public const ERROR_UPDATE_ENTRY = "Encontramos um problema ao atualizar esta entrada, tente mais tarde!";
    public const ERROR_UPLOAD_IMAGE_S3 = "Ocorreu um erro ao processar a imagem, tente mais tarde!";

    // Info messages
    public const INFO_AMOUNT_BY_ENTRY_TYPE_NO_RECORDS = "Sem informações...";
    public const INFO_NO_ENTRIES_FOUNDED = "Não foram encontradas entradas para este mês ou os filtros aplicados não retornaram resultados...";
    public const INFO_NO_ENTRY_FOUNDED = "Nenhum entrada encontrada...";
    public const INFO_UPDATED_ENTRY = "Entrada atualizada com sucesso!";


}
