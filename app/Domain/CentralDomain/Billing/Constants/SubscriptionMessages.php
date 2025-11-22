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

    // Success Messages
    const SUCCESS_SUBSCRIPTION_CREATED = 'Assinatura criada com sucesso';

    const SUCCESS_SUBSCRIPTION_CANCELLED = 'Assinatura cancelada com sucesso';

    const SUCCESS_PAYMENT_METHOD_UPDATED = 'Método de pagamento atualizado com sucesso';
}
