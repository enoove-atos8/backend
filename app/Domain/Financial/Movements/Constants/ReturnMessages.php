<?php

namespace Domain\Financial\Movements\Constants;

class ReturnMessages
{
// Success messages

    public const INITIAL_BALANCE_MOVEMENT_CREATED = 'Movimentação inicial criada com sucesso!';

    // Error messages

    public const MOVEMENTS_NOT_FOUND = 'Não encontramos nenhuma movimentação!';
    public const MOVEMENTS_DELETE_ERROR = 'Não foi possível excluir as movimentações do grupo';
    public const INITIAL_MOVEMENT_CREATE_ERROR = 'Erro ao criar um movimento inicial, tente novamente mais tarde!';

    // Info messages
}
