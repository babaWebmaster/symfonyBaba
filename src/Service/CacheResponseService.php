<?php
// src/Service/CacheResponseService.php
namespace App\Service;

use Symfony\Component\HttpFoundation\Response;

class CacheResponseService
{
    public function applyCache(Response $response, int $maxAge = 3600, int $sharedMaxAge = 86400): Response
    {
        $response->setPublic();
        $response->setMaxAge($maxAge);           // cache navigateur
        $response->setSharedMaxAge($sharedMaxAge); // cache CDN / proxy
        $response->headers->addCacheControlDirective('must-revalidate', true);

        return $response;
    }
}
?>