<?php

namespace App\EventSubscriber;

use League\Flysystem\FilesystemOperator;
use Liip\ImagineBundle\Events\CacheResolveEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class LiipImagineFilterSubscriber implements EventSubscriberInterface
{
    private FilesystemOperator $imageThumbnailFilesystem;

    public function __construct(FilesystemOperator $imageThumbnailFilesystem)
    {
        $this->imageThumbnailFilesystem = $imageThumbnailFilesystem;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'liip_imagine.post_resolve' => 'onPostResolve'
        ];
    }

    public function onPostResolve(CacheResolveEvent $event): void
    {
//        $path = $event->getPath();
//        $filter = $event->getFilter();
//
//        $date = new \DateTime();
//        // We set the expiration in 10 minutes for example.
//        $date = $date->add(new \DateInterval('PT10M'));
//
//        $url = $this->imageThumbnailFilesystem->temporaryUrl($path, $date);
//
//        if (isset($url)) {
//            $event->setUrl($url);
//        }
    }
}