<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Tests\Helpers\Traits\HasFakerHelpers;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    use HasFakerHelpers;
}
