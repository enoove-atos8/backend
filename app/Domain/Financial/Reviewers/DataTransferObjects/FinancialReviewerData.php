<?php

namespace App\Domain\Financial\Reviewers\DataTransferObjects;

use Spatie\DataTransferObject\DataTransferObject;

class FinancialReviewerData extends DataTransferObject
{
    /** @var integer | null */
    public ?int $id;

    /** @var string | null */
    public ?string $fullName;

    /** @var string | null */
    public ?string $reviewerType;

    /** @var string | null */
    public ?string $avatar;

    /** @var string | null */
    public ?string $gender;

    /** @var string | null */
    public ?string $cpf;

    /** @var string | null */
    public ?string $rg;

    /** @var string | null */
    public ?string $email;

    /** @var string | null */
    public ?string $cellPhone;

    /** @var boolean | null */
    public ?bool $activated;

    /** @var boolean | null */
    public ?bool $deleted;

    /** @var boolean | null */
    public ?bool $rememberToken;

}
