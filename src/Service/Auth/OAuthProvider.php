<?php

namespace App\Service\Auth;

enum OAuthProvider: string
{
    case Apple = 'apple';
    case Google = 'google';
}
