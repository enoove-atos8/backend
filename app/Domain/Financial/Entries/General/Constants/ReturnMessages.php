<?php

namespace App\Domain\Financial\Entries\General\Constants;

class ReturnMessages
{
    // Success messages
    public const SUCCESS_ENTRY_REGISTERED = "Entrada registrada com sucesso!";
    public const ENTRY_DELETED = "Entrada excluída com sucesso!";
    public const ENTRY_RECEIPT_PROCESSED = "Comprovante processado com sucesso!";

    // Error messages
    public const ERROR_UPDATE_ENTRY = "Encontramos um problema ao atualizar esta entrada, tente mais tarde!";
    public const ERROR_CREATE_ENTRY = "Encontramos um problema ao cadastrar esta entrada, tente mais tarde!";
    public const ERROR_UPLOAD_IMAGE_S3 = "Ocorreu um erro ao processar a imagem, tente mais tarde!";
    public const ERROR_DELETED_ENTRY = "Ocorreu um erro ao excluir esta entrada!";
    public const ERROR_NO_COMPENSATE_ENTRIES_NOT_FOUND = "Não existe entradas a serem compensadas!";

    // Info messages
    public const INFO_AMOUNT_BY_ENTRY_TYPE_NO_RECORDS = "Sem informações...";
    public const INFO_NO_ENTRIES_FOUNDED = "Não foram encontradas entradas para este mês ou os filtros aplicados não retornaram resultados...";
    public const INFO_NO_ENTRY_FOUNDED = "Nenhum entrada encontrada...";
    public const INFO_UPDATED_ENTRY = "Entrada atualizada com sucesso!";


}
