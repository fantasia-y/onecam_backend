<?php

namespace App\Entity\Auth;

use Gesdinet\JWTRefreshTokenBundle\Entity\RefreshToken as BaseRefreshToken;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table('refresh_tokens')]
class RefreshToken extends BaseRefreshToken
{
}
