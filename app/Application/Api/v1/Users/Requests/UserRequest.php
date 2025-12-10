<?php

namespace Application\Api\v1\Users\Requests;

use App\Domain\Accounts\Users\DataTransferObjects\UserData;
use App\Domain\Accounts\Users\DataTransferObjects\UserDetailData;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class UserRequest extends FormRequest
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
            'id'                    =>  'integer',
            'email'                 =>  ['required', 'email', Rule::unique('users', 'email')->ignore($this->id)],
            'activated'             =>  'required',
            'type'                  =>  'required',
            'changedPassword'       =>  'required',
            'accessQuantity'        =>  'required',
            'roles'                 =>  'required',
            'details.fullName'      =>  'required',
            'details.avatar'        =>  '',
            'details.type'          =>  '',
            'details.title'         =>  '',
            'details.gender'        =>  'required',
            'details.phone'         =>  '',
            'details.address'       =>  '',
            'details.district'      =>  '',
            'details.city'          =>  '',
            'details.country'       =>  '',
            'details.birthday'      =>  'required',

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

        ];
    }

    /**
     * Function to data transfer objects to UserData class
     *
     * @throws UnknownProperties
     */
    public function userData(): UserData
    {
        return new UserData(
            email:              $this->input('email'),
            activated:          $this->input('activated'),
            type:               $this->input('type'),
            changedPassword:    $this->input('changedPassword'),
            accessQuantity:     $this->input('accessQuantity'),
            roles:              $this->input('roles'),
        );
    }


    /**
     * Function to data transfer objects to UserDetailData class
     *
     * @return UserDetailData
     * @throws UnknownProperties
     */
    public function userDetailData(): UserDetailData
    {
        return new UserDetailData(
            name:        $this->input('details.fullName'),
            avatar:      $this->input('details.avatar'),
            type:        $this->input('details.type'),
            title:       $this->input('details.title'),
            gender:      $this->input('details.gender'),
            phone:       $this->input('details.phone'),
            address:     $this->input('details.address'),
            district:    $this->input('details.district'),
            city:        $this->input('details.city'),
            country:     $this->input('details.country'),
            birthday:    $this->input('details.birthday'),
        );
    }
}
