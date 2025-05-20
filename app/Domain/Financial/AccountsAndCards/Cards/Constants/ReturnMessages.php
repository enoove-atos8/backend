<?php

namespace Domain\Financial\AccountsAndCards\Cards\Constants;

class ReturnMessages
{

    // Success messages

    public const CARD_CREATED = 'Cartão salvo com sucesso!';
    public const CARD_DELETED = 'Cartão excluído com sucesso!';

    // Error messages

    public const CARDS_NOT_FOUND = 'Nenhum cartão por aqui';
    public const CARDS_NOT_CREATED = 'Houve um erro ao tentar criar um cartão de crédito';
    public const DELETED_CARD_ERROR = 'Houve um erro ao excluir este cartão de crédito, tente mais tarde!';

    // Info messages
}
