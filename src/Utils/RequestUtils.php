<?php

namespace App\Utils;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

class RequestUtils
{
    public static function toRedirectResponse(string $url, Response $response): RedirectResponse
    {
        if ($response instanceof RedirectResponse) {
            return $response;
        }

        if ($response->headers->get('Content-Type') === 'application/json') {
            $jsonData = json_decode($response->getContent());
            $urlParams = http_build_query($jsonData);
            if (!empty($urlParams)) {
                $url .= '&' . $urlParams;
            }
        }

        return new RedirectResponse($url);
    }
}