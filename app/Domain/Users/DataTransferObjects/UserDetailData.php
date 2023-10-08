<?php

namespace Domain\Users\DataTransferObjects;

use Spatie\DataTransferObject\DataTransferObject;

class UserDetailData extends DataTransferObject
{
    /** @var integer  */
    public int $user_id = 0;

    /** @var string  */
    public string $full_name;

    /** @var string|null  */
    public string|null $avatar;

    /** @var string|null  */
    public string|null $type;

    /** @var string|null  */
    public string|null $title;

    /** @var string  */
    public string $gender;

    /** @var string  */
    public string $phone;

    /** @var string  */
    public string $address;

    /** @var string|null  */
    public string|null $district;

    /** @var string|null  */
    public string|null $city;

    /** @var string|null  */
    public string|null $country;

    /** @var string  */
    public string $birthday;
}
