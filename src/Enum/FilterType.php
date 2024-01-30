<?php

namespace App\Enum;

enum FilterType: string
{
    case NONE = 'image';
    case THUMBNAIL = 'image_thumbnail';
}
