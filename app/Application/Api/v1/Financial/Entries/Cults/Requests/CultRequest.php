<?php

namespace Application\Api\v1\Financial\Entries\Cults\Requests;

use App\Domain\Financial\Entries\Consolidation\DataTransferObjects\ConsolidationEntriesData;
use App\Domain\Financial\Entries\Cults\DataTransferObjects\CultData;
use Illuminate\Foundation\Http\FormRequest;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class CultRequest extends FormRequest
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
            'reviewerId'                    =>  'required',
            'worshipWithoutEntries'         =>  'required',
            'cultDay'                       =>  'required',
            'cultDate'                      =>  'required',
            'dateTransactionCompensation'   =>  '',
            'accountId'                     =>  'required',
            'transactionType'               =>  'required',
            'transactionCompensation'       =>  'required',
            'tithe'                         =>  '',
            'designated'                    =>  '',
            'offer'                         =>  '',
            'devolution'                    =>  '',
            'deleted'                       =>  'required',
            'receipt'                       =>  !$this->input('worshipWithoutEntries') ? 'required' : 'nullable'
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
            'worshipWithoutEntries.required'            =>  "É necessário informar se existem valores obtidos neste culto ou não!",
            'cultDay.required'                          =>  "Informe o dia do culto!",
            'cultDate.required'                         =>  "O preenchimento do campo 'Data do culto' é obrigatório!",
            'dateTransactionCompensation.required'      =>  "O preenchimento do campo 'Data de depósito' é obrigatório!",
            'accountId.required'                        =>  "É necessário informar a conta em que o valor foi depositado!",
            'transactionType.required'                  =>  "É necessário informar o tipo de transação!",
            'transactionCompensation.required'          =>  "É necessário informar o status de compensação da transação!",
            'deleted.required'                          =>  "É necessário informar o status de deleção da entrada!",
            'reviewerId.required'                       =>  "É necessário informar o revisor",
            'receipt.required'                          =>  "É necessário anexar um comprovante!",
        ];
    }


    /**
     * Function to data transfer objects to CultData class
     *
     * @return CultData
     * @throws UnknownProperties
     */
    public function cultData(): CultData
    {
        return new CultData(
            worshipWithoutEntries:         $this->input('worshipWithoutEntries'),
            cultDay:                       $this->input('cultDay'),
            cultDate:                      $this->input('cultDate'),
            dateTransactionCompensation:   $this->input('dateTransactionCompensation'),
            accountId:                     $this->input('accountId'),
            transactionType:               $this->input('transactionType'),
            transactionCompensation:       $this->input('transactionCompensation'),
            tithes:                        $this->input('tithe'),
            designated:                    $this->input('designated'),
            offer:                         $this->input('offer'),
            deleted:                       $this->input('deleted'),
            reviewerId:                    $this->input('reviewerId'),
            receipt:                       $this->input('receipt'),
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
