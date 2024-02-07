<?php

namespace App\Enum;

enum FilterType: string
{
    case NONE = 'original';
    case THUMBNAIL = 'thumbnail';
}
