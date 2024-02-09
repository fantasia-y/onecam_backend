<?php

namespace App\Enum;

enum FilterPrefix: string
{
    case IMAGE = 'image_';
    case USER = 'user_';
    case GROUP = 'group_';
}
