<?php

namespace App\Domain\CentralDomain\Billing\Constants;

class InvoiceMessages
{
    // Error Messages
    const ERROR_INVOICE_NOT_FOUND = 'Fatura não encontrada';

    const ERROR_INVOICE_ALREADY_PAID = 'Esta fatura já foi paga';

    const ERROR_INVOICE_VOIDED = 'Esta fatura foi cancelada';

    const ERROR_INVALID_PAYMENT_METHOD = 'Método de pagamento inválido. Use boleto ou pix';

    const ERROR_MISSING_TAX_ID = 'CPF/CNPJ é obrigatório para pagamento via Boleto';

    const ERROR_MISSING_BILLING_ADDRESS = 'Endereço de cobrança é obrigatório para pagamento via Boleto';

    const ERROR_CREATE_PAYMENT_INTENT = 'Erro ao criar intenção de pagamento';

    const ERROR_INVOICE_NOT_BELONGS_TO_CUSTOMER = 'Esta fatura não pertence a este cliente';

    const ERROR_NO_STRIPE_CUSTOMER = 'Igreja não possui customer no Stripe';

    const ERROR_CHURCH_NOT_FOUND = 'Igreja não encontrada';

    // Success Messages
    const SUCCESS_BOLETO_GENERATED = 'Boleto gerado com sucesso';

    const SUCCESS_PIX_GENERATED = 'PIX gerado com sucesso';

    const SUCCESS_PAYMENT_CONFIRMED = 'Pagamento confirmado com sucesso';

    // Date Formats
    const DATE_FORMAT_BR = 'd/m/Y';

    const DATETIME_FORMAT_BR = 'd/m/Y H:i';

    const DATE_FORMAT_YEAR_MONTH = 'Y-m';

    // Invoice Number Prefix
    const INVOICE_NUMBER_PREFIX = 'Fatura #';
}
