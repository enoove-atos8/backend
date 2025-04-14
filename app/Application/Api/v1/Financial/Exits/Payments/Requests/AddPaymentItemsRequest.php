<?php

namespace Application\Api\v1\Financial\Exits\Payments\Requests;

use App\Domain\Financial\Entries\Consolidation\DataTransferObjects\ConsolidationEntriesData;
use App\Domain\Financial\Entries\Entries\DataTransferObjects\EntryData;
use App\Domain\Financial\Exits\Payments\Items\DataTransferObjects\PaymentItemData;
use Illuminate\Foundation\Http\FormRequest;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class AddPaymentItemsRequest extends FormRequest
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
     * @return array
     */
    public function rules(): array
    {
        return [
            'paymentCategoryId'   =>  'required',
            'name'                =>  'required',
            'description'         =>  'required',
            'slug'                =>  'required',
        ];
    }




    /**
     * Custom message for validation
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'paymentCategoryId.required'  =>  "O preenchimento do campo 'paymentCategoryId' é obrigatório!",
            'name.required'               =>  "O preenchimento do campo 'Nome' é obrigatório!",
            'description.required'        =>  "O preenchimento do campo 'Descrição' é obrigatório!",
            'slug.required'               =>  "A informação de 'slug' é obrigatória!",
        ];
    }


    /**
     * Function to data transfer objects to EntryData class
     *
     * @return PaymentItemData
     * @throws UnknownProperties
     */
    public function paymentItemData(): PaymentItemData
    {
        return new PaymentItemData(
            paymentCategoryId:   $this->input('paymentCategoryId'),
            slug:                $this->input('slug'),
            name:                $this->input('name'),
            description:         $this->input('description'),
        );
    }
}
