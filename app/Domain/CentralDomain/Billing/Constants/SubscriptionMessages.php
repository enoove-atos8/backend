<?php

namespace App\Domain\CentralDomain\Billing\Constants;

class SubscriptionMessages
{
    // Error Messages
    const ERROR_PLAN_NOT_FOUND = 'Plano não encontrado ou sem Price ID configurado no Stripe';

    const ERROR_ATTACH_PAYMENT_METHOD = 'Erro ao anexar método de pagamento ao cliente';

    const ERROR_SET_DEFAULT_PAYMENT_METHOD = 'Erro ao definir método de pagamento padrão';

    const ERROR_CREATE_SUBSCRIPTION = 'Erro ao criar assinatura no Stripe';

    const ERROR_PROCESS_SUBSCRIPTION = 'Erro ao processar assinatura';

    const ERROR_SAVE_SUBSCRIPTION = 'Erro ao salvar assinatura no banco de dados';

    const ERROR_PAYMENT_METHOD_NOT_FOUND = 'Método de pagamento não encontrado';

    const ERROR_DETACH_PAYMENT_METHOD = 'Erro ao remover método de pagamento';

    const ERROR_CANNOT_DELETE_DEFAULT_PAYMENT_METHOD = 'Não é possível remover o método de pagamento padrão';

    // Success Messages
    const SUCCESS_SUBSCRIPTION_CREATED = 'Assinatura criada com sucesso';

    const SUCCESS_SUBSCRIPTION_CANCELLED = 'Assinatura cancelada com sucesso';

    const SUCCESS_PAYMENT_METHOD_UPDATED = 'Método de pagamento atualizado com sucesso';

    const SUCCESS_PAYMENT_METHOD_ADDED = 'Método de pagamento adicionado com sucesso';

    const SUCCESS_PAYMENT_METHOD_DELETED = 'Método de pagamento removido com sucesso';

    const SUCCESS_DEFAULT_PAYMENT_METHOD_SET = 'Método de pagamento padrão definido com sucesso';
}
