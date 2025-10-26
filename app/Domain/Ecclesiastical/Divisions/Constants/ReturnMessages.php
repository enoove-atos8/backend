<?php

namespace Domain\Ecclesiastical\Divisions\Constants;

class ReturnMessages
{
    //SUCCESS MESSAGES
    public const DIVISION_CREATED = 'Divisão criada com sucesso!';


    //ERROR MESSAGES
    public const ERROR_CREATE_DIVISION = 'Houve um erro ao criar esta divisão, tente novamente mais tarde!';
    public const ERROR_ALREADY_DIVISION = 'Já existe uma divisão criada com este nome!';
    public const DIVISIONS_NOT_FOUND = 'Não existem divisões cadastradas no momento!';
}
