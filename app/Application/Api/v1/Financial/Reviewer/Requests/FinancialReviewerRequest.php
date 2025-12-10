<?php

namespace App\Application\Api\v1\Financial\Reviewer\Requests;

use App\Domain\Financial\Reviewers\DataTransferObjects\FinancialReviewerData;
use Illuminate\Foundation\Http\FormRequest;

class FinancialReviewerRequest extends FormRequest
{
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
            'reviewers' => 'required|array|min:1',
            'reviewers.*.id' => 'nullable|integer',
            'reviewers.*.fullName' => 'required|string|max:255',
            'reviewers.*.reviewerType' => 'required|string|max:100',
            'reviewers.*.avatar' => 'nullable|string',
            'reviewers.*.gender' => 'required|string|in:male,female',
            'reviewers.*.cpf' => 'nullable|string|max:14',
            'reviewers.*.rg' => 'nullable|string|max:20',
            'reviewers.*.email' => 'nullable|email|max:255',
            'reviewers.*.cellPhone' => 'required|string|max:20',
            'reviewers.*.activated' => 'nullable|boolean',
        ];
    }

    /**
     * Get custom validation messages
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'reviewers.required' => 'A lista de revisores é obrigatória.',
            'reviewers.array' => 'A lista de revisores deve ser um array.',
            'reviewers.min' => 'É necessário informar pelo menos um revisor.',
            'reviewers.*.fullName.required' => 'O nome completo é obrigatório.',
            'reviewers.*.reviewerType.required' => 'O tipo de revisor é obrigatório.',
            'reviewers.*.gender.required' => 'O gênero é obrigatório.',
            'reviewers.*.gender.in' => 'O gênero deve ser male ou female.',
            'reviewers.*.cellPhone.required' => 'O celular é obrigatório.',
            'reviewers.*.email.email' => 'O e-mail informado não é válido.',
        ];
    }

    /**
     * Convert request to array of FinancialReviewerData DTOs
     *
     * @return FinancialReviewerData[]
     */
    public function financialReviewersData(): array
    {
        return array_map(function ($reviewer) {
            return new FinancialReviewerData(
                id: $reviewer['id'] ?? null,
                fullName: $reviewer['fullName'],
                reviewerType: $reviewer['reviewerType'],
                avatar: $reviewer['avatar'] ?? null,
                gender: $reviewer['gender'],
                cpf: $reviewer['cpf'] ?? null,
                rg: $reviewer['rg'] ?? null,
                email: $reviewer['email'] ?? null,
                cellPhone: $reviewer['cellPhone'],
                activated: $reviewer['activated'] ?? true,
                deleted: false,
                rememberToken: null,
            );
        }, $this->input('reviewers'));
    }
}
