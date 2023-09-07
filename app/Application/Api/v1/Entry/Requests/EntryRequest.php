<?php

namespace Application\Api\v1\Entry\Requests;

use Domain\Entries\DataTransferObjects\EntryData;
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
            'member.memberId'                =>  $this->validatorField('memberId'),
            'reviewer.reviewerId'            =>  'required',
        ];
    }

    public function validatorField($field)
    {
        $entryType = $this->input('entryType');
        $statusCompensation = $this->input('transactionCompensation');

        //Validate required to compensation date field
        if($field === 'dateTransactionCompensation')
        {
            if($statusCompensation === 'to_compensate')
            {
                return '';
            }
            else
            {
                return 'required';
            }
        }
        if($field === 'recipient')
        {
            if($entryType === self::DESIGNATED)
            {
                return 'required';
            }
            else
            {
                return '';
            }
        }

        if($field === 'memberId')
        {
            if($entryType === self::TITHE)
            {
                return 'required';
            }
            else
            {
                return '';
            }
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

        ];
    }

    /**
     * Function to data transfer objects to ChurchData class
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
            memberId:                       $this->input('member.memberId'),
            reviewerId:                     $this->input('reviewer.reviewerId'),
        );
    }
}
