<?php

namespace App\Domain\Accounts\Users\Constants;

class ReturnMessages
{
    // Success messages
    public const SUCCESS_USER_REGISTERED = "Usuário registrado com sucesso!";
    public const SUCCESS_UPDATED_USER = "Usuário atualizado com sucesso!";
    public const SUCCESS_UPDATE_STATUS_USER = "Status do usuário atualizado!";
    public const SUCCESS_UPDATE_IMAGE_USER = "Imagem processada com sucesso!";
    public const SUCCESS_CHANGE_PASSWORD = "Senha alterada com sucesso!";
    public const ERROR_UPLOAD_IMAGE_S3 = "Ocorreu um erro ao processar a imagem, tente mais tarde!";

    // Error messages
    public const ERROR_UPDATE_USER = "Encontramos um problema ao atualizar este usuário, tente mais tarde!";
    public const ERROR_UPDATE_STATUS_USER = "Encontramos um problema ao atualizar o status deste usuário, tente mais tarde!";
    public const ERROR_UNAUTHORIZED = "Usuário não autenticado";
    public const ERROR_INCORRECT_CURRENT_PASSWORD = "Senha atual incorreta";
    public const ERROR_CHANGE_PASSWORD = "Erro ao alterar a senha, tente novamente mais tarde!";

    // Info messages
    public const INFO_NO_USER_FOUNDED = "Usuário não encontrado!";
    public const INFO_NO_USERS_FOUNDED = "Nenhum usuário encontrado!";

}
