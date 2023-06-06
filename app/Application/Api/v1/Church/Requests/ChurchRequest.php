<?php

namespace Application\Api\v1\Church\Requests;

use Domain\Churches\DataTransferObjects\ChurchData;
use Illuminate\Foundation\Http\FormRequest;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class ChurchRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'tenant_id'                         =>  'required|unique:tenants,id',
            'name'                              =>  'required|string',
            'activated'                         =>  'required|boolean',
            'doc_type'                          =>  'required|string',
            'doc_number'                        =>  'required|string',
            'admin_email_tenant'                =>  'required|string',
            'pass_admin_email_tenant'           =>  'required|string',
            'confirm_pass_admin_email_tenant'   =>  'required|string|same:pass_admin_email_tenant',
        ];
    }


    /**
     * Custom message for validation
     *
     * @return array
     */
    public function messages()
    {
        return [
            'tenant_id.required'                         => 'O Preenchimento do campo tenant_id é obrigatório',
            'tenant_id.unique'                           => 'Já existe um usuário cadastrado com estas iniciais',
            'name.required'                              => 'O Preenchimento do campo name é obrigatório',
            'name.string'                                => 'O tipo do campo name é inválido',
            'activated.required'                         => 'O Preenchimento do campo activated é obrigatório',
            'activated.boolean'                          => 'O tipo do campo activated é inválido',
            'doc_type.required'                          => 'O Preenchimento do campo doc_type é obrigatório',
            'doc_type.string'                            => 'O tipo do campo doc_type é inválido',
            'doc_number.required'                        => 'O Preenchimento do campo doc_number é obrigatório',
            'doc_number.string'                          => 'O tipo do campo doc_number é inválido',
            'admin_email_tenant.required'                => 'O Preenchimento do campo admin_email_tenant é obrigatório',
            'admin_email_tenant.string'                  => 'O tipo do campo admin_email_tenant é inválido',
            'pass_admin_email_tenant.required'           => 'O Preenchimento do campo pass_admin_email_tenant é obrigatório',
            'pass_admin_email_tenant.string'             => 'O tipo do campo pass_admin_email_tenant é inválido',
            'confirm_pass_admin_email_tenant.required'   => 'O Preenchimento do campo confirm_pass_admin_email_tenant é obrigatório',
            'confirm_pass_admin_email_tenant.string'     => 'O tipo do campo confirm_pass_admin_email_tenant é inválido',
            'confirm_pass_admin_email_tenant.same'       => 'A senha de confirmação deve ser o mesmo do campo senha',
        ];
    }

    /**
     * Function to data transfer objects to ChurchData class
     *
     * @return ChurchData
     * @throws UnknownProperties
     */
    public function churchData(): ChurchData
    {
        return new ChurchData(
            tenantId:             $this->input('tenant_id'),
            name:                 $this->input('name'),
            activated:            $this->input('activated'),
            docType:              $this->input('doc_type'),
            docNumber:            $this->input('doc_number'),
            adminEmailTenant:     $this->input('admin_email_tenant'),
            passAdminEmailTenant: $this->input('pass_admin_email_tenant'),
        );
    }
}
