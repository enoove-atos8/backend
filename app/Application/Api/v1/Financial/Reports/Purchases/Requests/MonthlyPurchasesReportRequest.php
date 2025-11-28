<?php

namespace App\Application\Api\v1\Financial\Reports\Purchases\Requests;

use App\Domain\Financial\Reports\Purchases\DataTransferObjects\MonthlyPurchasesReportData;
use Illuminate\Foundation\Http\FormRequest;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class MonthlyPurchasesReportRequest extends FormRequest
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
            'cardId'                            =>  '',
            'reportName'                        =>  'required',
            'detailedReport'                    =>  '',
            'generationDate'                    =>  'required',
            'dates'                             =>  'required',
            'status'                            =>  'required',
            'startedBy'                         =>  'required',
            'dateOrder'                         =>  '',
            'allCardsReceipts'                  =>  '',
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
            'reportName.required'       =>  "Informe o nome do relatório para realizar o processamento!",
            'generationDate.required'   =>  "É obrigatório informar a data de geração do relatório!",
            'dates.required'            =>  "É obrigatório informar o intervalo de datas para a geração deste relatório!",
            'status.required'           =>  "É obrigatório informar o status inicial deste relatório!",
            'startedBy.required'        =>  "É obrigatório informar o usuário de solicitou a geração deste relatório!",
        ];
    }




    /**
     * Function to data transfer objects to MonthlyPurchasesReportData class
     *
     * @return MonthlyPurchasesReportData
     * @throws UnknownProperties
     */
    public function monthlyPurchasesReportData(): MonthlyPurchasesReportData
    {
        return new MonthlyPurchasesReportData(
            cardId:                             $this->input('cardId'),
            reportName:                         $this->input('reportName'),
            detailedReport:                     $this->input('detailedReport'),
            generationDate:                     $this->input('generationDate'),
            dates:                              $this->input('dates'),
            status:                             $this->input('status'),
            startedBy:                          $this->input('startedBy'),
            dateOrder:                          $this->input('dateOrder'),
            allCardsReceipts:                   $this->input('allCardsReceipts'),
        );
    }
}
