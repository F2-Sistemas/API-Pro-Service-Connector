<?php

namespace Tests\Helpers\Traits;

use Tests\Helpers\FakerHelpers;

trait HasFakerHelpers
{
    public static function validMobileNumber(): string
    {
        return FakerHelpers::validMobileNumber();
    }

    public static function inValidMobileNumber(): string
    {
        return substr(static::validMobileNumber(), 0, 5);
    }
}
