<?php

namespace Application\Api\v1\Ecclesiastical\Groups\Requests;

use App\Domain\Financial\Entries\Consolidated\DataTransferObjects\ConsolidationEntriesData;
use App\Domain\Financial\Entries\General\DataTransferObjects\EntryData;
use Domain\Ecclesiastical\Groups\DataTransferObjects\GroupData;
use Illuminate\Foundation\Http\FormRequest;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class GroupRequest extends FormRequest
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
            'groupName'             =>  'required',
            'groupLeaderId'         =>  '',
            'financialMovement'     =>  '',
            'returnValues'          =>  '',
            'returnReceivingGroup'  =>  '',
            'divisionId'            =>  'required',
            'parentGroupId'         =>  '',
            'description'           =>  '',
            'enabled'               =>  '',
            'temporaryEvent'        =>  '',
            'startDate'             =>  '',
            'endDate'               =>  '',
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
            'groupName.required'          =>  "O preenchimento do nome do grupo é obrigatório!",
            'divisionId.required'         =>  "É necessário informar uma divisão eclesiástica!",
        ];
    }


    /**
     * Function to data transfer objects to GroupData class
     *
     * @return GroupData
     * @throws UnknownProperties
     */
    public function groupData(): GroupData
    {
        return new GroupData(
            groupName:              $this->input('groupName'),
            leaderId:               $this->input('groupLeaderId'),
            financialMovement:      $this->input('financialMovement'),
            returnValues:           $this->input('returnValues'),
            returnReceivingGroup:   $this->input('returnReceivingGroup'),
            divisionId:             $this->input('divisionId'),
            parentGroupId:          $this->input('parentGroupId'),
            description:            $this->input('description'),
            enabled:                $this->input('enabled'),
            temporaryEvent:         $this->input('temporaryEvent'),
            startDate:              $this->input('startDate'),
            endDate:                $this->input('endDate'),
        );
    }
}
