<?php

namespace Application\Api\v1\Mobile\SyncStorage\Requests;

use App\Domain\SyncStorage\DataTransferObjects\SyncStorageData;
use Illuminate\Foundation\Http\FormRequest;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class ReceiptDataRequest extends FormRequest
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
            'tenant'                                => 'required',
            'module'                                => 'required',
            'docType'                               => 'required',
            'docSubType'                            => 'required',
            'divisionId'                            => '',
            'groupId'                               => '',
            'paymentCategoryId'                     => '',
            'paymentItemId'                         => '',
            'cardId'                                => '',
            'invoiceClosedDay'                      => '',
            'isPayment'                             => 'required',
            'isDevolution'                          => '',
            'isCreditCardPurchase'                  => '',
            'creditCardDueDate'                     => '',
            'numberInstallments'                    => '',
            'purchaseCreditCardInstallmentAmount'   => '',
            'purchaseCreditCardDate'                => '',
            'purchaseCreditCardAmount'              => '',
            'status'                                => 'required',
            'path'                                  => 'required',
            'file'                                  => 'required',
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
            'tenant.required'               => "O campo tenant é obrigatório.",
            'module.required'               => "O campo module é obrigatório.",
            'docType.required'              => "O campo docType é obrigatório.",
            'docSubType.required'           => "O campo docSubType é obrigatório.",
            'cardId.required'               => "O campo cardId é obrigatório.",
            'isPayment.required'            => "O campo isPayment é obrigatório.",
            'isCreditCardPurchase.required' => "O campo isCreditCardPurchase é obrigatório.",
            'status.required'               => "O campo status é obrigatório.",
            'path.required'                 => "O campo path é obrigatório.",
        ];
    }


    /**
     * @throws UnknownProperties
     */
    public function syncStorageData(): SyncStorageData
    {
        return new SyncStorageData(
            tenant:                                 $this->input('tenant'),
            module:                                 $this->input('module'),
            docType:                                $this->input('docType'),
            docSubType:                             $this->input('docSubType'),
            divisionId:                             $this->integer('divisionId'),
            groupId:                                $this->integer('groupId'),
            paymentCategoryId:                      $this->integer('paymentCategoryId'),
            paymentItemId:                          $this->integer('paymentItemId'),
            cardId:                                 $this->integer('cardId'),
            invoiceClosedDay:                       $this->integer('invoiceClosedDay'),
            isPayment:                              $this->boolean('isPayment'),
            isDevolution:                           $this->boolean('isDevolution'),
            isCreditCardPurchase:                   $this->boolean('isCreditCardPurchase'),
            creditCardDueDate:                      $this->input('creditCardDueDate'),
            numberInstallments:                     $this->input('numberInstallments'),
            purchaseCreditCardInstallmentAmount:    $this->input('purchaseCreditCardInstallmentAmount'),
            purchaseCreditCardDate:                 $this->input('purchaseCreditCardDate'),
            purchaseCreditCardAmount:               $this->input('purchaseCreditCardAmount'),
            status:                                 $this->input('status'),
            path:                                   $this->input('path'),
        );
    }
}
