<?php

namespace Domain\Secretary\Membership\Constants;

class ReturnMessages
{
    // Success messages
    public const SUCCESS_MEMBER_REGISTERED = 'Membro registrado com sucesso!';

    public const SUCCESS_UPDATED_MEMBER = 'Membro atualizado com sucesso...';

    public const SUCCESS_UPDATE_STATUS_MEMBER = 'Status do membro atualizado!';

    public const SUCCESS_UPDATE_IMAGE_MEMBER = 'Imagem processada com sucesso!';

    public const SUCCESS_DELETED_MEMBER = 'Imagem processada com sucesso!';

    public const SUCCESS_MEMBERS_ADDED_TO_GROUP = 'Membros adicionados ao grupo com sucesso!';

    // Error messages
    public const ERROR_UPDATE_MEMBER = 'Encontramos um problema ao atualizar este membro, tente mais tarde!';

    public const ERROR_UPDATE_STATUS_MEMBER = 'Encontramos um problema ao atualizar o status deste membro, tente mais tarde!';

    public const ERROR_ADD_MEMBERS_TO_GROUP = 'Erro ao adicionar membros ao grupo, tente mais tarde!';

    // Info messages
    public const INFO_NO_MEMBER_FOUNDED = 'Membro não encontrado...';

    public const INFO_NO_MEMBERS_FOUNDED = 'Nenhum membro encontrado...';
}
