<?php

namespace App\Enums;

class BalanceType extends Enum
{
    public const PAY_BALANCE = 1;

    public const ADD_BALANCE_FROM_ADMIN_PANEL = 2;

    public const DEDUCT_BALANCE_FROM_ADMIN_PANEL = 3;
}
