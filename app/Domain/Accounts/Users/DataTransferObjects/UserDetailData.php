<?php

namespace App\Domain\Accounts\Users\DataTransferObjects;

use Spatie\DataTransferObject\DataTransferObject;

class UserDetailData extends DataTransferObject
{
    /** @var integer  */
    public int $id = 0;

    /** @var string|null  */
    public string|null $name;

    /** @var string|null|array  */
    public string|null|array $avatar;

    /** @var string|null  */
    public string|null $type;

    /** @var string|null  */
    public string|null $title;

    /** @var string|null  */
    public string|null $gender;

    /** @var string|null  */
    public string|null $phone;

    /** @var string|null  */
    public string|null $address;

    /** @var string|null  */
    public string|null $district;

    /** @var string|null  */
    public string|null $city;

    /** @var string|null  */
    public string|null $country;

    /** @var string|null  */
    public string|null $birthday;
}
