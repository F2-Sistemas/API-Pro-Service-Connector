<?php

namespace App\Enums;

enum ProjectStatus: int
{
    case OPEN_TO_PROPOSALS = 10;
    case CANCELLED = 20;
    case FINISHED = 30;
}
