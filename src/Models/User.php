<?php

namespace ShineOnCom\Zoho\Models;

/**
 * Class User
 *
 * @property int $id
 * @property string $name
 */
class User extends AbstractModel
{
    /** @var string $resource_name */
    public static $resource_name = 'user';

    /** @var string $resource_name_many */
    public static $resource_name_many = 'users';

    /** @var array $dates */
    protected $dates = [
        'Last_Activity_Time',
        'Created_Time',
        'Modified_Time',
    ];

    /** @var array $casts */
    protected $casts = [];
}
