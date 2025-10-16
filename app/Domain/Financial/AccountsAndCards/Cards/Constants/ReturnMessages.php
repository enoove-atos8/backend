<?php

namespace Domain\Financial\AccountsAndCards\Cards\Constants;

class ReturnMessages
{

    // Success messages

    public const CARD_CREATED = 'Cartão salvo com sucesso!';
    public const CARD_DELETED = 'Cartão excluído com sucesso!';
    public const CARD_DEACTIVATE = 'Cartão desativado com sucesso!';

    // Error messages

    public const CARDS_NOT_FOUND = 'Nenhum cartão por aqui';
    public const INVOICE_NOT_FOUND = 'Ainda não existe fatura para este mês!';
    public const CARDS_NOT_CREATED = 'Houve um erro ao tentar criar um cartão de crédito';
    public const PURCHASE_NOT_CREATED = 'Houve um erro ao tentar criar uma compra';
    public const INSTALLMENT_NOT_CREATED = 'Houve um erro ao tentar criar a parcela desta compra';
    public const PURCHASES_NOT_FOUND = 'Nenhuma compra por aqui!';
    public const INVOICE_NOT_CREATED = 'Houve um problema ao criar a fatura';
    public const DELETED_CARD_ERROR = 'Houve um erro ao excluir este cartão de crédito, tente mais tarde!';

    // Info messages
}
