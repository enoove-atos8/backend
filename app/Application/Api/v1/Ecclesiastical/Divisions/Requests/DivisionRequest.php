<?php

namespace Application\Api\v1\Ecclesiastical\Divisions\Requests;

use App\Domain\Financial\Entries\Consolidated\DataTransferObjects\ConsolidationEntriesData;
use App\Domain\Financial\Entries\Entries\DataTransferObjects\EntryData;
use Domain\Ecclesiastical\Divisions\DataTransferObjects\DivisionData;
use Domain\Ecclesiastical\Groups\DataTransferObjects\GroupData;
use Illuminate\Foundation\Http\FormRequest;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class DivisionRequest extends FormRequest
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
            'routeResource'     =>  'required',
            'name'              =>  'required',
            'description'       =>  '',
            'requireLeader'     =>  '',
            'enabled'           =>  'required',
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
            'name.required'             =>  "O preenchimento do nome da divisão é obrigatório!",
            'routeResource.required'    =>  "O Route Resource deve ser enviado juntamente com os dados preenchidos, tente novamente mais tarde!",
            'enabled.required'          =>  "O campo enabled deve ser enviado juntamente com os dados preenchidos, tente novamente mais tarde!",
        ];
    }


    /**
     * Function to data transfer objects to DivisionData class
     *
     * @return DivisionData
     * @throws UnknownProperties
     */
    public function divisionData(): DivisionData
    {
        return new DivisionData(
            routeResource:      $this->input('routeResource'),
            name:               $this->input('name'),
            description:        $this->input('description'),
            requireLeader:      $this->input('requireLeader'),
            enabled:            $this->input('enabled'),
        );
    }
}
