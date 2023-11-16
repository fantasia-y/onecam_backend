<?php

namespace App\Request;

use Gesdinet\JWTRefreshTokenBundle\Request\Extractor\ExtractorInterface;
use Symfony\Component\HttpFoundation\Request;

class RefreshTokenExtractor implements ExtractorInterface
{

    public function getRefreshToken(Request $request, string $parameter): ?string
    {
        return $request->headers->get('X-Refresh-Token');
    }
}