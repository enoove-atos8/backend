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
        $dateTransactionCompensation = $this->input('dateTransactionCompensation');
        $recipient = $this->input('recipient');

        //Recipient field mount validator
        if($entryType === self::TITHE and $field === 'recipient'){return '';}
        if($entryType === self::OFFERS and $field === 'recipient'){return '';}
        if($entryType === self::DESIGNATED and $field === 'recipient'){return 'required';}
        if($recipient === null){return '';}

        //memberName, memberId and memberAvatar field mount validator
        if($entryType === self::TITHE and ($field === 'memberId' or $field === 'memberName' or $field === 'memberAvatar')){return 'required';}
        if($entryType === self::OFFERS and ($field === 'memberId' or $field === 'memberName' or $field === 'memberAvatar')){return '';}
        if($entryType === self::DESIGNATED and ($field === 'memberId' or $field === 'memberName' or $field === 'memberAvatar')){return '';}

        // dateTransactionCompensation field mount validator
        if($dateTransactionCompensation === null){return '';}
        if($dateTransactionCompensation === 'to_compensate'){return '';}
        if($dateTransactionCompensation === 'compensated'){return 'required';}
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
            dateTransactionCompensation:    $this->input('dateTransactionCompensation') ? $this->input('dateTransactionCompensation') !== null : '',
            dateEntryRegister:              $this->input('dateEntryRegister'),
            amount:                         $this->input('amount'),
            recipient:                      $this->input('recipient') ? $this->input('recipient') !== null : '',
            memberId:                       $this->input('member.memberId'),
            reviewerId:                     $this->input('reviewer.reviewerId'),
        );
    }
}
