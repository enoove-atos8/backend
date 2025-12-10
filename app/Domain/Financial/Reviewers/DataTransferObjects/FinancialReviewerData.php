<?php

namespace App\Domain\Financial\Reviewers\DataTransferObjects;

use App\Domain\Financial\Reviewers\Models\FinancialReviewer;
use Spatie\DataTransferObject\DataTransferObject;

class FinancialReviewerData extends DataTransferObject
{
    /** @var int|null */
    public ?int $id;

    /** @var string|null */
    public ?string $fullName;

    /** @var string|null */
    public ?string $reviewerType;

    /** @var string|null */
    public ?string $avatar;

    /** @var string|null */
    public ?string $gender;

    /** @var string|null */
    public ?string $cpf;

    /** @var string|null */
    public ?string $rg;

    /** @var string|null */
    public ?string $email;

    /** @var string|null */
    public ?string $cellPhone;

    /** @var bool|null */
    public ?bool $activated;

    /** @var bool|null */
    public ?bool $deleted;

    /** @var bool|null */
    public ?bool $rememberToken;

    /**
     * Cria um FinancialReviewerData a partir de um Model FinancialReviewer
     */
    public static function fromSelf(FinancialReviewer $reviewer): self
    {
        return new self([
            'id' => $reviewer->id,
            'fullName' => $reviewer->full_name,
            'reviewerType' => $reviewer->reviewer_type,
            'avatar' => $reviewer->avatar,
            'gender' => $reviewer->gender,
            'cpf' => $reviewer->cpf,
            'rg' => $reviewer->rg,
            'email' => $reviewer->email,
            'cellPhone' => $reviewer->cell_phone,
            'activated' => (bool) $reviewer->activated,
            'deleted' => (bool) $reviewer->deleted,
            'rememberToken' => null,
        ]);
    }
}
