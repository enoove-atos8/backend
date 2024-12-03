<?php

namespace Application\Api\v1\Financial\Entries\Cults\Requests;

use App\Domain\Financial\Entries\Consolidated\DataTransferObjects\ConsolidationEntriesData;
use App\Domain\Financial\Entries\Cults\DataTransferObjects\CultData;
use Illuminate\Foundation\Http\FormRequest;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class CultRequest extends FormRequest
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
            'worshipWithoutEntries'         =>  'required',
            'cultDay'                       =>  'required',
            'cultDate'                      =>  'required',
            'dateTransactionCompensation'   =>  '',
            'transactionType'               =>  'required',
            'transactionCompensation'       =>  'required',
            'tithe'                         =>  '',
            'designated'                    =>  '',
            'offer'                         =>  '',
            'devolution'                    =>  '',
            'deleted'                       =>  'required',
            'reviewerId'                    =>  'required',
            'receipt'                       =>  $this->input('worshipWithoutEntries') == false ? 'required' : 'nullable'
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
            'worshipWithoutEntries.required'            =>  "É necessário informar existem valores obtidos neste culto ou não!",
            'cultDay.required'                          =>  "Informe o dia do culto!",
            'cultDate.required'                         =>  "O preenchimento do campo 'Data do culto' é obrigatório!",
            'dateTransactionCompensation.required'      =>  "O preenchimento do campo 'Data de depósito' é obrigatório!",
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
            transactionType:               $this->input('transactionType'),
            transactionCompensation:       $this->input('transactionCompensation'),
            tithes:                        $this->input('tithe'),
            designated:                    $this->input('designated'),
            offers:                        $this->input('offer'),
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
