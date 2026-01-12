<?php

namespace App\Domain\Ecclesiastical\Groups\Groups\Constants;

class ReturnMessages
{
    // SUCCESS MESSAGES
    public const GROUP_CREATED = 'Grupo criado com sucesso!';

    public const GROUP_LEADER_UPDATED = 'Líder do grupo atualizado com sucesso!';

    public const GROUP_STATUS_UPDATED = 'Status do grupo atualizado com sucesso!';

    public const GROUP_DELETED = 'Grupo excluído com sucesso!';

    public const MINISTERIAL_LIMIT_UPDATED = 'Limite de Investimento Ministerial atualizado com sucesso!';

    // ERROR MESSAGES
    public const ERROR_UPDATE_GROUP_LEADER = 'Erro ao atualizar o líder do grupo!';

    public const ERROR_UPDATE_GROUP_STATUS = 'Erro ao atualizar o status do grupo!';

    public const ERROR_UPDATE_MINISTERIAL_LIMIT = 'Erro ao atualizar o limite de Investimento Ministerial!';

    public const ERROR_DELETE_GROUP = 'Erro ao excluir o grupo!';

    public const ERROR_DELETE_GROUP_HAS_BALANCE = 'Não é possível excluir o grupo, pois existe saldo disponível!';

    public const ERROR_CREATE_GROUP = 'Houve um erro ao criar este grupo, tente novamente mais tarde!';

    public const GROUP_NOT_FOUNDED = 'Este grupo eclesiástico não foi localidado!';

    public const GROUP_ALREADY = 'Este grupo eclesiástico já existe!';

    public const GROUP_NOT_FOUND = 'Grupo eclesiástico não encontrado!';
}
