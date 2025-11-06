<?php

namespace App\Application\Api\v1\Financial\AccountsAndCards\Accounts\Requests\Files;

use App\Domain\Financial\AccountsAndCards\Accounts\DataTransferObjects\AccountFileData;
use App\Domain\Financial\AccountsAndCards\Accounts\Models\AccountsFiles;
use Illuminate\Foundation\Http\FormRequest;
use Infrastructure\Repositories\Financial\AccountsAndCards\Accounts\AccountFilesRepository;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class AccountFileRequest extends FormRequest
{
    private ?AccountsFiles $existingFile = null;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'accountId' => 'required',
            'originalFilename' => 'required',
            'link' => '',
            'fileType' => 'required',
            'version' => 'required',
            'referenceDate' => 'required',
            'status' => '',
            'errorMessage' => '',
            'replaceExisting' => '',
            'deleted' => '',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $this->existingFile = AccountsFiles::where(AccountFilesRepository::REFERENCE_DATE_COLUMN, $this->referenceDate)
                ->where(AccountFilesRepository::ACCOUNT_ID_COLUMN, $this->accountId)
                ->where(AccountFilesRepository::DELETED_COLUMN, false)
                ->first();

            if ($this->existingFile && ! $this->boolean('replaceExisting')) {
                $validator->errors()->add(
                    'referenceDate',
                    'Já existe um arquivo para essa data de referência, se desejar marque para substituir o arquivo existente.'
                );
            }
        });
    }

    /**
     * Custom message for validation
     */
    public function messages(): array
    {
        return [
            'accountId.required' => "O preenchimento do campo 'accountId' é obrigatório!",
            'originalFilename.required' => "O preenchimento do campo 'originalFilename' é obrigatório!",
            'fileType.required' => "O preenchimento do campo 'fileType' é obrigatório!",
            'version.required' => "A informação de 'version' é obrigatória!",
            'referenceDate.required' => "O preenchimento do campo 'referenceDate' é obrigatório!",
        ];
    }

    /**
     * Convert request to CardData DTO
     *
     * @throws UnknownProperties
     */
    public function accountFileData(): AccountFileData
    {
        return new AccountFileData(
            id: $this->existingFile?->id,
            accountId: $this->input('accountId'),
            originalFilename: $this->input('originalFilename'),
            link: $this->input('link'),
            fileType: $this->input('fileType'),
            version: $this->input('version'),
            referenceDate: $this->input('referenceDate'),
            status: $this->input('status'),
            errorMessage: $this->input('errorMessage'),
            replaceExisting: $this->input('replaceExisting'),
            deleted: $this->input('deleted'),
        );
    }
}
