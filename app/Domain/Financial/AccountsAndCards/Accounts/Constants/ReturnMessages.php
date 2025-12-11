<?php

namespace Domain\Financial\AccountsAndCards\Accounts\Constants;

class ReturnMessages
{

    // Success messages

    public const ACCOUNT_CREATED = 'Conta salva com sucesso!';
    public const ACCOUNT_DEACTIVATED = 'Conta desativada com sucesso!';
    public const ACCOUNT_DELETED = 'Conta excluída com sucesso!';

    // Error messages

    public const ACCOUNT_NOT_CREATED = 'Ocorreu um erro ao criar a conta';
    public const ACCOUNT_NOT_DELETED = 'Houve um erro ao excluir esta conta, tente mais tarde!';
    public const FILE_NOT_CREATED = 'Ocorreu um erro ao cadastrar esse arquivo';
    public const FILES_NOT_FOUND = 'Nenhum arquivo por aqui';

    // Info messages
}
