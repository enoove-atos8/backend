<?php

namespace Domain\Ecclesiastical\Groups\Constants;

class ReturnMessages
{
    // SUCCESS MESSAGES
    public const GROUP_CREATED = 'Grupo criado com sucesso!';

    public const GROUP_LEADER_UPDATED = 'Líder do grupo atualizado com sucesso!';

    // ERROR MESSAGES
    public const ERROR_UPDATE_GROUP_LEADER = 'Erro ao atualizar o líder do grupo!';

    public const ERROR_CREATE_GROUP = 'Houve um erro ao criar este grupo, tente novamente mais tarde!';

    public const GROUP_NOT_FOUNDED = 'Este grupo eclesiástico não foi localidado!';

    public const GROUP_ALREADY = 'Este grupo eclesiástico já existe!';

    public const GROUP_NOT_FOUND = 'Grupo eclesiástico não encontrado!';
}
