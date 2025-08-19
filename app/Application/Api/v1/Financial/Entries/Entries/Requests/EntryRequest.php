<?php

namespace Application\Api\v1\Financial\Entries\Entries\Requests;

use App\Domain\Financial\Entries\Consolidation\DataTransferObjects\ConsolidationEntriesData;
use App\Domain\Financial\Entries\Entries\DataTransferObjects\EntryData;
use Illuminate\Foundation\Http\FormRequest;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class EntryRequest extends FormRequest
{
    const TITHE = 'tithe';
    const OFFER = 'offer';
    const DESIGNATED = 'designated';


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
            'memberId'                      =>  '',
            'accountId'                      =>  '',
            'reviewerId'                    =>  'required',
            'cultId'                        =>  '',
            'groupReturnedId'               =>  '',
            'groupReceivedId'               =>  '',
            'identificationPending'         =>  '',
            'entryType'                     =>  'required',
            'transactionType'               =>  $this->validatorField('transactionType'),
            'transactionCompensation'       =>  $this->validatorField('transactionCompensation'),
            'dateTransactionCompensation'   =>  $this->validatorField('dateTransactionCompensation'),
            'dateEntryRegister'             =>  'required',
            'amount'                        =>  'required',
            'timestampValueCpf'             =>  '',
            'devolution'                    =>  '',
            'residualValue'                 =>  '',
            'deleted'                       =>  '',
            'comments'                      =>  '',
            'receipt'                       =>  $this->validatorField('receipt'),
        ];
    }


    /**
     * Custom message for validation
     *
     * @param $field
     * @return string|void
     */
    public function validatorField($field)
    {
        $entryType = $this->input('entryType');
        $residualValue = $this->input('residualValue');
        $statusCompensation = $this->input('transactionCompensation');

        //Validate required to compensation date field
        if($field === 'dateTransactionCompensation')
        {
            if($statusCompensation === 'to_compensate' || $residualValue)
            {
                return '';
            }
            else
            {
                return 'required';
            }
        }

        if($field === 'transactionCompensation')
        {
            if($residualValue)
            {
                return '';
            }
            else
            {
                return 'required';
            }
        }

        if($field === 'transactionType')
        {
            if($residualValue)
            {
                return '';
            }
            else
            {
                return 'required';
            }
        }

        if($field === 'receipt')
        {
            if($statusCompensation === 'to_compensate' || $residualValue)
            {
                return '';
            }
            else
            {
                return 'required';
            }
        }
    }




    /**
     * Custom message for validation
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'entryType.required'                =>  "O preenchimento do campo 'Tipo de Entrada' é obrigatório!",
            'transactionType.required'          =>  "O preenchimento do campo 'Tipo de Transação' é obrigatório!",
            'transactionCompensation.required'  =>  "O preenchimento do campo 'Status Compensação' é obrigatório!",
            'dateEntryRegister.required'        =>  "A informação de 'Data de registro' é obrigatória!",
            'amount.required'                   =>  "O preenchimento do campo 'Valor' é obrigatório!",
            'reviewerId.required'               =>  "O preenchimento do campo 'Revisor' é obrigatório!",
            'deleted.required'                  =>  "A informação de 'deleted' é obrigatória para a entrada!",
            'receipt.required'                  =>  "O cadastro do comprovante é obrigatório, verifique!",
        ];
    }




    /**
     * Function to data transfer objects to EntryData class
     *
     * @return EntryData
     * @throws UnknownProperties
     */
    public function entryData(): EntryData
    {
        return new EntryData(
            memberId:                       $this->input('memberId'),
            accountId:                       $this->input('accountId'),
            reviewerId:                     $this->input('reviewerId'),
            cultId:                         $this->input('cultId'),
            groupReturnedId:                $this->input('groupReturnedId'),
            groupReceivedId:                $this->input('groupReceivedId'),
            identificationPending:          $this->input('identificationPending'),
            entryType:                      $this->input('entryType'),
            transactionType:                $this->input('transactionType'),
            transactionCompensation:        $this->input('transactionCompensation'),
            dateTransactionCompensation:    $this->input('dateTransactionCompensation'),
            dateEntryRegister:              $this->input('dateEntryRegister'),
            amount:                         $this->input('amount'),
            timestampValueCpf:              $this->input('timestampValueCpf'),
            devolution:                     $this->input('devolution'),
            residualValue:                  $this->input('residualValue'),
            deleted:                        $this->input('deleted'),
            comments:                       $this->input('comments'),
            receipt:                        $this->input('receipt'),
        );
    }




    /**
     * Function to data transfer objects to ConsolidationEntriesData class
     *
     * @return ConsolidationEntriesData
     * @throws UnknownProperties
     */
    public function consolidationEntriesData(): ConsolidationEntriesData
    {
        return new ConsolidationEntriesData(
            date:           $this->input('dateTransactionCompensation'),
        );
    }
}
