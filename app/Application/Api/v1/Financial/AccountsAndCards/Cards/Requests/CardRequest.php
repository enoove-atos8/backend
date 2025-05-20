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
            'id'                => '',
            'name'              => 'required',
            'description'       => '',
            'cardNumber'        => 'required',
            'expiryDate'        => 'required',
            'dueDay'            => 'required',
            'closingDay'        => 'required',
            'status'            => 'required',
            'active'            => 'required',
            'deleted'           => 'required',
            'creditCardBrand'   => 'required',
            'personType'        => '',
            'cardHolderName'    => '',
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
            id:   $this->input('id'),
            name:   $this->input('name'),
            description:    $this->input('description'),
            cardNumber: $this->input('cardNumber'),
            expiryDate: $this->input('expiryDate'),
            dueDay:    $this->input('dueDay'),
            closingDay:    $this->input('closingDay'),
            status: $this->input('status'),
            active: $this->input('active'),
            deleted: $this->input('deleted'),
            creditCardBrand:    $this->input('creditCardBrand'),
            personType: $this->input('personType'),
            cardHolderName: $this->input('cardHolderName'),
            limit:  $this->input('limit'),
        );
    }
}
