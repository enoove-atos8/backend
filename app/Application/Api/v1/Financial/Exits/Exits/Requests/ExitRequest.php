<?php

namespace Application\Api\v1\Financial\Exits\Exits\Requests;

use App\Domain\Financial\Exits\Payments\Categories\DataTransferObjects\PaymentCategoryData;
use App\Domain\Financial\Exits\Payments\Items\DataTransferObjects\PaymentItemData;
use App\Domain\Financial\Reviewers\DataTransferObjects\FinancialReviewerData;
use Domain\Ecclesiastical\Divisions\DataTransferObjects\DivisionData;
use Domain\Ecclesiastical\Groups\DataTransferObjects\GroupData;
use Domain\Financial\Exits\Exits\DataTransferObjects\ExitData;
use Illuminate\Foundation\Http\FormRequest;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class ExitRequest extends FormRequest
{
    const PAYMENTS = 'payments';
    const TRANSFER = 'transfer';
    const MINISTERIAL_TRANSFER = 'ministerial_transfer';
    const CONTRIBUTIONS = 'contributions';


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
            'accountId'                         =>  '',
            'amount'                            =>  'required',
            'dateExitRegister'                  =>  'required',
            'dateTransactionCompensation'       =>  'required',
            'deleted'                           =>  'required',
            'divisionId'                        =>  '',
            'exitType'                          =>  'required',
            'groupId'                           =>  '',
            'isPayment'                         =>  'required',
            'paymentCategoryId'                 =>  '',
            'paymentItemId'                     =>  '',
            'receiptLink'                       =>  'required',
            'reviewerId'                        =>  'required',
            'transactionCompensation'           =>  'required',
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
            'amount.required'                       =>  "O preenchimento do campo 'Valor' é obrigatório!",
            'dateExitRegister.required'             =>  "O preenchimento do campo 'Data de registro' é obrigatório!",
            'dateTransactionCompensation.required'  =>  "O preenchimento do campo 'Data de transação' é obrigatório!",
            'deleted.required'                      =>  "A informação de 'deleted' é obrigatória!",
            'exitType.required'                     =>  "O preenchimento do campo 'Tipo de saída' é obrigatório!",
            'isPayment.required'                    =>  "É necessário informat se essa saída é um pagamento!",
            'receiptLink.required'                  =>  "A informação de 'recibo' é obrigatório!",
            'reviewerId.required'                   =>  "É obrigatório informar o revisor!",
            'transactionCompensation.required'      =>  "è obrigatório informar a compensação da transação!",
        ];
    }




    /**
     * Function to data transfer objects to EntryData class
     *
     * @return ExitData
     * @throws UnknownProperties
     */
    public function exitData(): ExitData
    {
        return new ExitData(
            accountId:                     $this->input('accountId'),
            exitType:                      $this->input('exitType'),
            isPayment:                     $this->input('isPayment'),
            deleted:                       $this->input('deleted'),
            transactionType:               $this->input('transactionType'),
            transactionCompensation:       $this->input('transactionCompensation'),
            dateTransactionCompensation:   $this->input('dateTransactionCompensation'),
            dateExitRegister:              $this->input('dateExitRegister'),
            timestampExitTransaction:      $this->input('timestampExitTransaction'),
            amount:                        $this->input('amount'),
            comments:                      $this->input('comments'),
            receiptLink:                   $this->input('receiptLink'),
            financialReviewer:             new FinancialReviewerData(['id' => $this->input('reviewerId')]),
            division:                      new DivisionData(['id' => $this->input('divisionId')]),
            group:                         new GroupData(['id' => $this->input('groupId')]),
            paymentCategory:               new PaymentCategoryData(['id' => $this->input('paymentCategoryId')]),
            paymentItem:                   new PaymentItemData(['id' => $this->input('paymentItemId')]),
        );
    }
}
