<?php

namespace App\Application\Api\v1\Financial\Entry\Requests;

use App\Domain\Financial\Entries\Consolidated\DataTransferObjects\ConsolidationEntriesData;
use App\Domain\Financial\Entries\General\DataTransferObjects\EntryData;
use Illuminate\Foundation\Http\FormRequest;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class EntryRequest extends FormRequest
{
    const TITHE = 'tithe';
    const OFFERS = 'offers';
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
            'entryType'                      =>  'required',
            'transactionType'                =>  'required',
            'transactionCompensation'        =>  'required',
            'dateTransactionCompensation'    =>  $this->validatorField('dateTransactionCompensation'),
            'dateEntryRegister'              =>  'required',
            'amount'                         =>  'required',
            'recipient'                      =>  $this->validatorField('recipient'),
            'memberId'                       =>  '',
            'reviewerId'                     =>  'required',
            'devolution'                     =>  'integer',
            'deleted'                        =>  'required|integer',
            'comments'                       =>  '',
            'receipt'                        =>  $this->validatorField('receipt'),
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
        $statusCompensation = $this->input('transactionCompensation');

        //Validate required to compensation date field
        if($field === 'dateTransactionCompensation') {
            if($statusCompensation === 'to_compensate') {return '';}
            else {return 'required';}
        }
        if($field === 'receipt') {
            if($statusCompensation === 'to_compensate') {return '';}
            else {return 'required';}
        }
        if($field === 'recipient') {
            if($entryType === self::DESIGNATED) {return 'required';}
            else {return '';}
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
            'devolution.integer'                =>  "O valor do campo 'Devolução' deve ser um valor inteiro, 0 ou 1",
            'deleted.required'                  =>  "A informação de 'deleted' é obrigatória para a entrada!",
            'deleted.integer'                   =>  "O valor da informação 'deleted' deve ser um valor inteiro, 0 ou 1",
            'receipt.required'              =>  "O cadastro do comprovante é obrigatório, verifique!",
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
            entryType:                      $this->input('entryType'),
            transactionType:                $this->input('transactionType'),
            transactionCompensation:        $this->input('transactionCompensation'),
            dateTransactionCompensation:    $this->input('dateTransactionCompensation'),
            dateEntryRegister:              $this->input('dateEntryRegister'),
            amount:                         $this->input('amount'),
            recipient:                      $this->input('recipient'),
            memberId:                       $this->input('memberId'),
            reviewerId:                     $this->input('reviewerId'),
            devolution:                     $this->input('devolution'),
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
