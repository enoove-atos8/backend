<?php

namespace App\Application\Api\v1\Financial\Reports\Balances\Requests;

use App\Domain\Financial\Reports\Balances\DataTransferObjects\MonthlyBalancesReportData;
use Illuminate\Foundation\Http\FormRequest;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class MonthlyBalancesReportRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'reportName' => 'required',
            'generationDate' => 'required',
            'dates' => 'required',
            'status' => 'required',
            'startedBy' => 'required',
        ];
    }

    /**
     * Custom message for validation
     */
    public function messages(): array
    {
        return [
            'reportName.required' => 'Informe o nome do relatório para realizar o processamento!',
            'generationDate.required' => 'É obrigatório informar a data de geração do relatório!',
            'dates.required' => 'É obrigatório informar o intervalo de datas para a geração deste relatório!',
            'status.required' => 'É obrigatório informar o status inicial deste relatório!',
            'startedBy.required' => 'É obrigatório informar o usuário de solicitou a geração deste relatório!',
        ];
    }

    /**
     * Function to data transfer objects to MonthlyBalancesReportData class
     *
     * @throws UnknownProperties
     */
    public function monthlyBalancesReportData(): MonthlyBalancesReportData
    {
        return new MonthlyBalancesReportData(
            reportName: $this->input('reportName'),
            generationDate: $this->input('generationDate'),
            dates: $this->input('dates'),
            status: $this->input('status'),
            startedBy: $this->input('startedBy'),
        );
    }
}
