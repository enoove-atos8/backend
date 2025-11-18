<?php

namespace Application\Api\v1\Users\Requests;

use App\Domain\Auth\DataTransferObjects\ChangePasswordData;
use Illuminate\Foundation\Http\FormRequest;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class ChangePasswordRequest extends FormRequest
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
            'currentPassword'   =>  ['required', 'string', 'min:8'],
            'newPassword'       =>  ['required', 'string', 'min:8', 'different:currentPassword'],
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
            'currentPassword.required'      =>  'A senha atual é obrigatória',
            'currentPassword.min'           =>  'A senha atual deve ter no mínimo 8 caracteres',
            'newPassword.required'          =>  'A nova senha é obrigatória',
            'newPassword.min'               =>  'A nova senha deve ter no mínimo 8 caracteres',
            'newPassword.different'         =>  'A nova senha deve ser diferente da senha atual',
        ];
    }

    /**
     * Function to data transfer objects to ChangePasswordData class
     *
     * @throws UnknownProperties
     */
    public function changePasswordData(): ChangePasswordData
    {
        return new ChangePasswordData(
            currentPassword:    $this->input('currentPassword'),
            newPassword:        $this->input('newPassword'),
        );
    }
}
