<?php

namespace Application\Api\v1\Financial\Movements\Requests;

use App\Infrastructure\Repositories\Financial\Entries\Entries\EntryRepository;
use Domain\Financial\Movements\DataTransferObjects\MovementsData;
use Illuminate\Foundation\Http\FormRequest;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class AddInitialBalanceRequest extends FormRequest
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
            'groupId'           => 'required',
            'initialBalance'    => 'required'
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'groupId.required' => 'O ID do grupo é obrigatório',
            'initialBalance.required' => 'O valor do saldo inicial é obrigatório',
        ];
    }

    /**
     * Function to data transfer objects to EntryData class
     *
     * @return MovementsData
     * @throws UnknownProperties
     */
    public function movementsData(): MovementsData
    {
        return new MovementsData(
            groupId:              $this->input('groupId'),
            entryId:              null,
            exitId:               null,
            type:                 EntryRepository::ENTRY_TYPE,
            subType:              null,
            amount:               $this->input('initialBalance'),
            balance:              $this->input('initialBalance'),
            description:          'Initial movement',
            movementDate:         date('Y-m-d'),
            isInitialBalance:     true,
            deleted:              false,

        );
    }
}
