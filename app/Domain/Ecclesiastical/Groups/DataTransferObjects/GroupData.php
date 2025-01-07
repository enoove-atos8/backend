<?php

namespace Domain\Ecclesiastical\Groups\DataTransferObjects;

use Spatie\DataTransferObject\DataTransferObject;

class GroupData extends DataTransferObject
{
    /** @var integer  */
    public int $id = 0;

    /** @var null | string  */
    public null | string $groupName;

    /** @var null | boolean  */
    public null | bool $financialMovement;

    /** @var null | boolean  */
    public null | bool $returnValues;

    /** @var null | boolean  */
    public null | bool $returnReceivingGroup;

    /** @var null | string  */
    public null | string $divisionId;

    /** @var null | integer  */
    public null | int $parentGroupId;

    /** @var null | integer */
    public null | int $leaderId;


    /** @var null | string  */
    public null | string $description;


    /** @var null | boolean  */
    public null | bool $enabled;

    /** @var null | boolean  */
    public null | bool $financialGroup;

    /** @var null | boolean  */
    public null | bool $temporaryEvent;

    /** @var null | string  */
    public null | string $startDate;

    /** @var null | string  */
    public null | string $endDate;

}
