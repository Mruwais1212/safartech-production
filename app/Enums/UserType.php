<?php

namespace App\Enums;

class UserType extends Enum
{
    public const SUPER_ADMIN = 1;

    public const ADMIN = 2;

    public const SUPERVISOR = 3;

    public const CLIENT = 4;
}
