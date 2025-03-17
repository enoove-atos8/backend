<?php

namespace Domain\Ecclesiastical\Groups\DataTransferObjects;

use Spatie\DataTransferObject\DataTransferObject;

class GroupData extends DataTransferObject
{
    /** @var null | integer  */
    public ?int $id = 0;

    /** @var null | string  */
    public ?string $name;

    /** @var null | boolean  */
    public ?bool $financialMovement;

    /** @var null | boolean  */
    public ?bool $returnValues;

    /** @var null | boolean  */
    public ?bool $returnReceivingGroup;

    /** @var null | string  */
    public ?string $divisionId;

    /** @var null | integer  */
    public ?int $parentGroupId;

    /** @var null | integer */
    public ?int $leaderId;


    /** @var null | string  */
    public ?string $description;

    /** @var null | string  */
    public ?string $slug;


    /** @var null | boolean  */
    public ?bool $enabled;

    /** @var null | boolean  */
    public ?bool $financialGroup;

    /** @var null | boolean  */
    public ?bool $temporaryEvent;

    /** @var null | string  */
    public ?string $startDate;

    /** @var null | string  */
    public ?string $endDate;

}
