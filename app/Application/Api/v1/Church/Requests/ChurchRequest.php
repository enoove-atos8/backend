<?php

namespace Application\Api\v1\Church\Requests;

use App\Domain\Users\User\DataTransferObjects\UserData;
use App\Domain\Users\User\DataTransferObjects\UserDetailData;
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
            'church.tenant_id'                       =>  'required|unique:tenants,id',
            'church.plan_id'                         =>  'required|integer',
            'church.name'                            =>  'required|string',
            'church.activated'                       =>  'required|boolean',
            'church.doc_type'                        =>  'required|string',
            'church.doc_number'                      =>  'required|string|unique:churches,doc_number',
            'user.admin_email_tenant'                =>  'required|string',
            'user.pass_admin_email_tenant'           =>  'required|string',
            'user.confirm_pass_admin_email_tenant'   =>  'required|string|same:user.pass_admin_email_tenant',
            'user.user_activated_tenant'             =>  'required|boolean',
            'user.changed_password'                  =>  'required|boolean',
            'user.access_quantity'                   =>  'required|integer',
            'user.user_type_tenant'                  =>  'required|string',
            'user.roles'                             =>  'required',
            'details.fullName'                       =>  'required|string',
            'details.avatar'                         =>  '',
            'details.type'                           =>  '',
            'details.title'                          =>  '',
            'details.gender'                         =>  'required|string',
            'details.phone'                          =>  'required|string',
            'details.address'                        =>  '',
            'details.district'                       =>  '',
            'details.city'                           =>  '',
            'details.country'                        =>  '',
            'details.birthday'                       =>  '',
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
            'church.tenant_id.required'                       => 'O Preenchimento do campo tenant_id é obrigatório.',
            'church.tenant_id.unique'                         => 'Já existe um usuário cadastrado com estas iniciais.',
            'church.plan_id.required'                         => 'O Preenchimento do campo plan_id é obrigatório.',
            'church.plan_id.integer'                          => 'O tipo do campo plan_id é inválido.',
            'church.name.required'                            => 'O Preenchimento do campo name é obrigatório.',
            'church.name.string'                              => 'O tipo do campo name é inválido.',
            'church.activated.required'                       => 'O Preenchimento do campo activated é obrigatório.',
            'church.activated.boolean'                        => 'O tipo do campo activated é inválido.',
            'church.doc_type.required'                        => 'O Preenchimento do campo doc_type é obrigatório.',
            'church.doc_type.string'                          => 'O tipo do campo doc_type é inválido.',
            'church.doc_number.required'                      => 'O Preenchimento do campo doc_number é obrigatório.',
            'church.doc_number.string'                        => 'O tipo do campo doc_number é inválido.',
            'church.doc_number.unique'                        => 'Já existe um cliente cadastrado com este documento.',
            'user.admin_email_tenant.required'                => 'O Preenchimento do campo admin_email_tenant é obrigatório.',
            'user.admin_email_tenant.string'                  => 'O tipo do campo admin_email_tenant é inválido.',
            'user.pass_admin_email_tenant.required'           => 'O Preenchimento do campo pass_admin_email_tenant é obrigatório.',
            'user.pass_admin_email_tenant.string'             => 'O tipo do campo pass_admin_email_tenant é inválido.',
            'user.confirm_pass_admin_email_tenant.required'   => 'O Preenchimento do campo confirm_pass_admin_email_tenant é obrigatório.',
            'user.confirm_pass_admin_email_tenant.string'     => 'O tipo do campo confirm_pass_admin_email_tenant é inválido.',
            'user.confirm_pass_admin_email_tenant.same'       => 'A senha de confirmação deve ser o mesmo do campo senha.',
            'user.user_activated_tenant.required'             => 'O Preenchimento do campo user_activated_tenant é obrigatório.',
            'user.user_activated_tenant.boolean'              => 'O tipo do campo user_activated_tenant é inválido.',
            'user.user_type_tenant.required'                  => 'O Preenchimento do campo user_type_tenant é obrigatório.',
            'user.user_type_tenant.boolean'                   => 'O tipo do campo user_type_tenant é inválido.',
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
            tenantId:             $this->input('church.tenant_id'),
            planId:               $this->input('church.plan_id'),
            name:                 $this->input('church.name'),
            activated:            $this->input('church.activated'),
            docType:              $this->input('church.doc_type'),
            docNumber:            $this->input('church.doc_number'),
        );
    }

    /**
     * @return \App\Domain\Users\User\DataTransferObjects\UserData
     * @throws UnknownProperties
     */
    public function userData(): UserData
    {
        return new UserData(
            email:              $this->input('user.admin_email_tenant'),
            password:           $this->input('user.pass_admin_email_tenant'),
            changedPassword:    $this->input('user.changed_password'),
            accessQuantity:     $this->input('user.user_activated_tenant'),
            activated:          $this->input('user.user_activated_tenant'),
            type:               $this->input('user.user_type_tenant'),
            roles:              $this->input('user.roles'),
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
            full_name:   $this->input('details.fullName'),
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
