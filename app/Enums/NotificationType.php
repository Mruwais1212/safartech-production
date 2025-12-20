<?php

namespace App\Enums;

class NotificationType extends Enum
{
    public const MESSAGE = 1;

    public const GROUP_NOTIFICATION = 2;

    public const REGISTER = 3;

    public const ADD_BALANCE_FROM_ADMIN_PANEL = 4;

    public const DEDUCT_BALANCE_FROM_ADMIN_PANEL = 5;
}
