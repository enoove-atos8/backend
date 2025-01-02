<?php

namespace Application\Api\v1\Financial\Entries\Reports\Requests;

use App\Domain\Financial\Entries\Consolidation\DataTransferObjects\ConsolidationEntriesData;
use App\Domain\Financial\Entries\Entries\DataTransferObjects\EntryData;
use App\Domain\Financial\Entries\Reports\DataTransferObjects\ReportRequestsData;
use Illuminate\Foundation\Http\FormRequest;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class ReportRequestsRequest extends FormRequest
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
            'reportName'                =>  'required',
            'detailedReport'            =>  '',
            'generationDate'            =>  'required',
            'dates'                     =>  'required',
            'status'                    =>  'required',
            'startedBy'                 =>  'required',
            'entryTypes'                =>  'required',
            'groupReceivedId'           =>  '',
            'dateOrder'                 =>  '',
            'allGroupsReceipts'         =>  '',
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
     * Function to data transfer objects to EntryData class
     *
     * @return ReportRequestsData
     * @throws UnknownProperties
     */
    public function reportJobData(): ReportRequestsData
    {
        return new ReportRequestsData(
            reportName:             $this->input('reportName'),
            detailedReport:         $this->input('detailedReport'),
            generationDate:         $this->input('generationDate'),
            dates:                  $this->input('dates'),
            status:                 $this->input('status'),
            startedBy:              $this->input('startedBy'),
            entryTypes:             $this->input('entryTypes'),
            groupReceivedId:        $this->input('groupReceivedId'),
            dateOrder:              $this->input('dateOrder'),
            allGroupsReceipts:      $this->input('allGroupsReceipts'),
        );
    }
}
