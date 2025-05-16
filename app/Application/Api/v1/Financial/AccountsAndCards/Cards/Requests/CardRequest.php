<?php

namespace Application\Api\v1\Financial\AccountsAndCards\Cards\Requests;

use Domain\Financial\AccountsAndCards\Cards\DataTransferObjects\CardData;
use Illuminate\Foundation\Http\FormRequest;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class CardRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
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
        return [
            'name'              => 'required',
            'description'       => '',
            'cardNumber'        => 'required',
            'expiryDate'        => 'required',
            'closingDate'       => 'required',
            'status'            => 'required',
            'active'            => 'required',
            'creditCardBrand'   => 'required',
            'personType'        => '',
            'cardHolderName'    => 'required',
            'limit'             => 'required',
        ];
    }

    /**
     * Convert request to CardData DTO
     *
     * @return CardData
     * @throws UnknownProperties
     */
    public function cardData(): CardData
    {
        return new CardData(
            name:   $this->input('name'),
            description:    $this->input('description'),
            cardNumber: $this->input('cardNumber'),
            expiryDate: $this->input('expiryDate'),
            closingDate:    $this->input('closingDate'),
            status: $this->input('status'),
            active: $this->input('active'),
            creditCardBrand:    $this->input('creditCardBrand'),
            personType: $this->input('personType'),
            cardHolderName: $this->input('cardHolderName'),
            limit:  $this->input('limit'),
        );
    }
}
