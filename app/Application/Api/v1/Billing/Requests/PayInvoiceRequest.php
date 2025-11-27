<?php

namespace Application\Api\v1\Billing\Requests;

use App\Domain\CentralDomain\Billing\Constants\PaymentMethodType;
use Domain\CentralDomain\Billing\DataTransferObjects\PayInvoiceData;
use Illuminate\Foundation\Http\FormRequest;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class PayInvoiceRequest extends FormRequest
{
    const PAYMENT_METHOD_FIELD = 'payment_method';

    const TAX_ID_FIELD = 'tax_id';

    const NAME_FIELD = 'name';

    const EMAIL_FIELD = 'email';

    const ADDRESS_LINE1_FIELD = 'address.line1';

    const ADDRESS_CITY_FIELD = 'address.city';

    const ADDRESS_STATE_FIELD = 'address.state';

    const ADDRESS_POSTAL_CODE_FIELD = 'address.postal_code';

    const VALIDATION_REQUIRED = 'required';

    const VALIDATION_STRING = 'string';

    const VALIDATION_EMAIL = 'email';

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $allowedMethods = implode(',', PaymentMethodType::ALLOWED_TYPES);
        $requiredIfBoleto = 'required_if:' . self::PAYMENT_METHOD_FIELD . ',' . PaymentMethodType::BOLETO;

        return [
            self::PAYMENT_METHOD_FIELD => [
                self::VALIDATION_REQUIRED,
                self::VALIDATION_STRING,
                'in:' . $allowedMethods,
            ],
            self::TAX_ID_FIELD => [
                $requiredIfBoleto,
                self::VALIDATION_STRING,
            ],
            self::NAME_FIELD => [
                $requiredIfBoleto,
                self::VALIDATION_STRING,
            ],
            self::EMAIL_FIELD => [
                $requiredIfBoleto,
                self::VALIDATION_STRING,
                self::VALIDATION_EMAIL,
            ],
            self::ADDRESS_LINE1_FIELD => [
                $requiredIfBoleto,
                self::VALIDATION_STRING,
            ],
            self::ADDRESS_CITY_FIELD => [
                $requiredIfBoleto,
                self::VALIDATION_STRING,
            ],
            self::ADDRESS_STATE_FIELD => [
                $requiredIfBoleto,
                self::VALIDATION_STRING,
            ],
            self::ADDRESS_POSTAL_CODE_FIELD => [
                $requiredIfBoleto,
                self::VALIDATION_STRING,
            ],
        ];
    }

    /**
     * Custom messages for validation errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            self::PAYMENT_METHOD_FIELD . '.' . self::VALIDATION_REQUIRED => 'Método de pagamento é obrigatório',
            self::PAYMENT_METHOD_FIELD . '.in' => 'Método de pagamento deve ser boleto ou pix',
            self::TAX_ID_FIELD . '.required_if' => 'CPF/CNPJ é obrigatório para pagamento via Boleto',
            self::NAME_FIELD . '.required_if' => 'Nome é obrigatório para pagamento via Boleto',
            self::EMAIL_FIELD . '.required_if' => 'E-mail é obrigatório para pagamento via Boleto',
            self::EMAIL_FIELD . '.' . self::VALIDATION_EMAIL => 'E-mail inválido',
            self::ADDRESS_LINE1_FIELD . '.required_if' => 'Endereço é obrigatório para pagamento via Boleto',
            self::ADDRESS_CITY_FIELD . '.required_if' => 'Cidade é obrigatória para pagamento via Boleto',
            self::ADDRESS_STATE_FIELD . '.required_if' => 'Estado é obrigatório para pagamento via Boleto',
            self::ADDRESS_POSTAL_CODE_FIELD . '.required_if' => 'CEP é obrigatório para pagamento via Boleto',
        ];
    }

    /**
     * Function to data transfer objects to PayInvoiceData class
     *
     * @throws UnknownProperties
     */
    public function setPayInvoiceData(): PayInvoiceData
    {
        return new PayInvoiceData(
            paymentMethod: $this->input(self::PAYMENT_METHOD_FIELD),
            taxId: $this->input(self::TAX_ID_FIELD),
            name: $this->input(self::NAME_FIELD),
            email: $this->input(self::EMAIL_FIELD),
            addressLine1: $this->input(self::ADDRESS_LINE1_FIELD),
            addressCity: $this->input(self::ADDRESS_CITY_FIELD),
            addressState: $this->input(self::ADDRESS_STATE_FIELD),
            addressPostalCode: $this->input(self::ADDRESS_POSTAL_CODE_FIELD),
        );
    }
}
