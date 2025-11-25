<?php

namespace Application\Api\v1\Church\Requests;

use App\Domain\Accounts\Users\DataTransferObjects\UserData;
use App\Domain\Accounts\Users\DataTransferObjects\UserDetailData;
use Domain\CentralDomain\Churches\Church\DataTransferObjects\ChurchData;
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
            'church.tenantId' => 'required|unique:tenants,id',
            'church.planId' => 'required|integer',
            'church.memberCount' => 'nullable|integer|min:1',
            'church.name' => 'required|string',
            'church.activated' => 'required|boolean',
            'church.address' => 'nullable|string',
            'church.cellPhone' => 'nullable|string',
            'church.mail' => 'nullable|string|email',
            'church.docType' => 'required|string',
            'church.docNumber' => 'required|string|unique:churches,doc_number',
            'user.adminEmailTenant' => 'required|string|email',
            'user.passAdminEmailTenant' => 'required|string',
            'user.confirmPassAdminEmailTenant' => 'required|string|same:user.passAdminEmailTenant',
            'user.userActivatedTenant' => 'required|boolean',
            'user.changedPassword' => 'required|boolean',
            'user.accessQuantity' => 'required|integer',
            'user.userTypeTenant' => 'required|string',
            'user.roles' => 'required',
            'details.fullName' => 'required|string',
            'details.avatar' => '',
            'details.type' => '',
            'details.title' => '',
            'details.gender' => 'required|string',
            'details.phone' => 'required|string',
            'details.address' => '',
            'details.district' => '',
            'details.city' => '',
            'details.country' => '',
            'details.birthday' => '',
            'payment_method_id' => 'nullable|string',
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
            'church.tenantId.required' => 'O Preenchimento do campo tenantId é obrigatório.',
            'church.tenantId.unique' => 'Já existe um usuário cadastrado com estas iniciais.',
            'church.planId.required' => 'O Preenchimento do campo planId é obrigatório.',
            'church.planId.integer' => 'O tipo do campo planId é inválido.',
            'church.name.required' => 'O Preenchimento do campo name é obrigatório.',
            'church.name.string' => 'O tipo do campo name é inválido.',
            'church.activated.required' => 'O Preenchimento do campo activated é obrigatório.',
            'church.activated.boolean' => 'O tipo do campo activated é inválido.',
            'church.address.string' => 'O tipo do campo address é inválido.',
            'church.cellPhone.string' => 'O tipo do campo cellPhone é inválido.',
            'church.mail.string' => 'O tipo do campo mail é inválido.',
            'church.mail.email' => 'O campo mail deve ser um e-mail válido.',
            'church.docType.required' => 'O Preenchimento do campo docType é obrigatório.',
            'church.docType.string' => 'O tipo do campo docType é inválido.',
            'church.docNumber.required' => 'O Preenchimento do campo docNumber é obrigatório.',
            'church.docNumber.string' => 'O tipo do campo docNumber é inválido.',
            'church.docNumber.unique' => 'Já existe um cliente cadastrado com este documento.',
            'user.adminEmailTenant.required' => 'O Preenchimento do campo adminEmailTenant é obrigatório.',
            'user.adminEmailTenant.string' => 'O tipo do campo adminEmailTenant é inválido.',
            'user.adminEmailTenant.email' => 'O campo adminEmailTenant deve ser um e-mail válido.',
            'user.passAdminEmailTenant.required' => 'O Preenchimento do campo passAdminEmailTenant é obrigatório.',
            'user.passAdminEmailTenant.string' => 'O tipo do campo passAdminEmailTenant é inválido.',
            'user.confirmPassAdminEmailTenant.required' => 'O Preenchimento do campo confirmPassAdminEmailTenant é obrigatório.',
            'user.confirmPassAdminEmailTenant.string' => 'O tipo do campo confirmPassAdminEmailTenant é inválido.',
            'user.confirmPassAdminEmailTenant.same' => 'A senha de confirmação deve ser o mesmo do campo senha.',
            'user.userActivatedTenant.required' => 'O Preenchimento do campo userActivatedTenant é obrigatório.',
            'user.userActivatedTenant.boolean' => 'O tipo do campo userActivatedTenant é inválido.',
            'user.userTypeTenant.required' => 'O Preenchimento do campo userTypeTenant é obrigatório.',
            'user.userTypeTenant.boolean' => 'O tipo do campo userTypeTenant é inválido.',
        ];
    }

    /**
     * Function to data transfer objects to ChurchData class
     *
     * @throws UnknownProperties
     */
    public function churchData(): ChurchData
    {
        return new ChurchData(
            tenantId: $this->input('church.tenantId'),
            planId: $this->input('church.planId'),
            name: $this->input('church.name'),
            activated: $this->input('church.activated'),
            address: $this->input('church.address'),
            cellPhone: $this->input('church.cellPhone'),
            mail: $this->input('church.mail'),
            docType: $this->input('church.docType'),
            docNumber: $this->input('church.docNumber'),
            paymentMethodId: $this->input('payment_method_id'),
            memberCount: $this->input('church.memberCount'),
        );
    }

    /**
     * @throws UnknownProperties
     */
    public function userData(): UserData
    {
        return new UserData(
            email: $this->input('user.adminEmailTenant'),
            password: $this->input('user.passAdminEmailTenant'),
            changedPassword: $this->input('user.changedPassword'),
            accessQuantity: $this->input('user.accessQuantity'),
            activated: $this->input('user.userActivatedTenant'),
            type: $this->input('user.userTypeTenant'),
            roles: $this->input('user.roles'),
        );
    }

    /**
     * Function to data transfer objects to UserDetailData class
     *
     * @throws UnknownProperties
     */
    public function userDetailData(): UserDetailData
    {
        return new UserDetailData(
            name: $this->input('details.fullName'),
            avatar: $this->input('details.avatar'),
            type: $this->input('details.type'),
            title: $this->input('details.title'),
            gender: $this->input('details.gender'),
            phone: $this->input('details.phone'),
            address: $this->input('details.address'),
            district: $this->input('details.district'),
            city: $this->input('details.city'),
            country: $this->input('details.country'),
            birthday: $this->input('details.birthday'),
        );
    }
}
