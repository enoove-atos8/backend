<?php

namespace App\Application\Api\v1\Ecclesiastical\Groups\AmountRequests\Requests;

use Domain\Ecclesiastical\Groups\AmountRequests\Constants\ReturnMessages;
use Domain\Ecclesiastical\Groups\AmountRequests\DataTransferObjects\AmountRequestReminderData;
use Illuminate\Foundation\Http\FormRequest;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class CreateAmountRequestReminderRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'type' => 'required|string|in:request_created,request_approved,request_rejected,transfer_completed,proof_reminder,proof_urgent,proof_overdue,proof_received,devolution_required,request_closed',
            'channel' => 'required|string|in:whatsapp,email,system',
            'scheduledAt' => 'required|date',
            'metadata' => 'sometimes|array',
        ];
    }

    /**
     * Custom message for validation
     */
    public function messages(): array
    {
        return [
            'type.required' => 'O tipo de lembrete é obrigatório!',
            'type.in' => 'O tipo de lembrete é inválido!',
            'channel.required' => 'O canal de envio é obrigatório!',
            'channel.in' => 'O canal de envio é inválido!',
            'scheduledAt.required' => 'A data de agendamento é obrigatória!',
            'scheduledAt.date' => 'A data de agendamento deve ser uma data válida!',
        ];
    }

    /**
     * Function to data transfer objects to AmountRequestReminderData class
     *
     * @throws UnknownProperties
     */
    public function reminderData(int $amountRequestId): AmountRequestReminderData
    {
        return new AmountRequestReminderData(
            amountRequestId: $amountRequestId,
            type: $this->input('type'),
            channel: $this->input('channel'),
            scheduledAt: $this->input('scheduledAt'),
            status: ReturnMessages::STATUS_PENDING,
            metadata: $this->input('metadata'),
        );
    }
}
