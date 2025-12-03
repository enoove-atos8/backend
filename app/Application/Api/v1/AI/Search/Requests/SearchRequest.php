<?php

namespace App\Application\Api\v1\AI\Search\Requests;

use App\Domain\AI\Search\DataTransferObjects\AiSearchHistoryData;
use Illuminate\Foundation\Http\FormRequest;

class SearchRequest extends FormRequest
{
    public const ERROR_QUESTION_REQUIRED = 'A pergunta é obrigatória';

    public const ERROR_QUESTION_MIN = 'A pergunta deve ter pelo menos 5 caracteres';

    public const ERROR_QUESTION_MAX = 'A pergunta deve ter no máximo 500 caracteres';

    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<string>>
     */
    public function rules(): array
    {
        return [
            'question' => ['required', 'string', 'min:5', 'max:500'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'question.required' => self::ERROR_QUESTION_REQUIRED,
            'question.min' => self::ERROR_QUESTION_MIN,
            'question.max' => self::ERROR_QUESTION_MAX,
        ];
    }

    public function searchData(): AiSearchHistoryData
    {
        return new AiSearchHistoryData(
            id: null,
            userId: $this->user()->id,
            question: $this->input('question'),
            sqlGenerated: null,
            resultData: null,
            resultTitle: null,
            resultDescription: null,
            suggestedFollowup: null,
            executionTimeMs: 0,
            success: false,
            errorMessage: null,
            rateLimitExceeded: false,
            createdAt: null
        );
    }
}
