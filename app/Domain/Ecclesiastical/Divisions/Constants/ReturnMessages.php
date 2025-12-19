<?php

namespace Domain\Ecclesiastical\Divisions\Constants;

class ReturnMessages
{
    //SUCCESS MESSAGES
    public const DIVISION_CREATED = 'Divisão criada com sucesso!';
    public const DIVISION_STATUS_UPDATED = 'Status da divisão atualizado com sucesso!';
    public const DIVISION_REQUIRE_LEADER_UPDATED = 'Configuração de líder obrigatório atualizada com sucesso!';


    //ERROR MESSAGES
    public const ERROR_CREATE_DIVISION = 'Houve um erro ao criar esta divisão, tente novamente mais tarde!';
    public const ERROR_ALREADY_DIVISION = 'Já existe uma divisão criada com este nome!';
    public const DIVISIONS_NOT_FOUND = 'Não existem divisões cadastradas no momento!';
    public const ERROR_UPDATE_STATUS = 'Houve um erro ao atualizar o status da divisão!';
    public const ERROR_UPDATE_REQUIRE_LEADER = 'Houve um erro ao atualizar a configuração de líder obrigatório!';
    public const DIVISION_NOT_FOUND = 'Divisão não encontrada!';
}
