<?php

namespace App\Application\Api\v1\Dashboard\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MemberEngagementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'month' => 'nullable|date_format:Y-m',
        ];
    }

    public function messages(): array
    {
        return [
            'month.date_format' => "O parâmetro 'month' deve estar no formato YYYY-MM (ex: 2025-11)!",
        ];
    }

    public function getMonth(): ?string
    {
        return $this->input('month');
    }
}
