<?php

namespace Tests\Helpers;

class FakerHelpers
{
    public static function validMobileNumber(): string
    {
        return fake()->unique()->regexify('([14689][1-9])9([1-9]){8}');
    }
}
