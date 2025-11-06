<?php

namespace App\Application\Api\v1\Financial\Reports\Exits\Requests;

use App\Domain\Financial\Reports\Exits\DataTransferObjects\MonthlyExitsReportData;
use Illuminate\Foundation\Http\FormRequest;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class MonthlyExitsReportRequest extends FormRequest
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
            'reportName'                        =>  'required',
            'detailedReport'                    =>  '',
            'generationDate'                    =>  'required',
            'dates'                             =>  'required',
            'status'                            =>  'required',
            'startedBy'                         =>  'required',
            'exitTypes'                         =>  '',
            'dateOrder'                         =>  '',
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
     * Function to data transfer objects to MonthlyExitsReportData class
     *
     * @return \App\Domain\Financial\Reports\Exits\DataTransferObjects\MonthlyExitsReportData
     * @throws UnknownProperties
     */
    public function monthlyExitsReportData(): MonthlyExitsReportData
    {
        return new MonthlyExitsReportData(
            reportName:                         $this->input('reportName'),
            detailedReport:                     $this->input('detailedReport'),
            generationDate:                     $this->input('generationDate'),
            dates:                              $this->input('dates'),
            status:                             $this->input('status'),
            startedBy:                          $this->input('startedBy'),
            exitTypes:                          $this->input('exitTypes'),
            dateOrder:                          $this->input('dateOrder'),
        );
    }
}
