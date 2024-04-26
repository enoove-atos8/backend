<?php

namespace Application\Api\v1\Members\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MemberAvatarRequest extends FormRequest
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
            'avatar' => 'image|mimes:jpeg,png,jpg|max:2048',
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
            'avatar.image'      => 'O arquivo deve ser do tipo imagem!',
            'avatar.mimes'      => 'O avatar enviado está em um formato inválido!',
            'avatar.max'        => 'O tamanho máximo do avatar deve ser de até 2MB!',
            'tenant.required'   => 'O campo tenant deve ser informado!',
        ];
    }
}
