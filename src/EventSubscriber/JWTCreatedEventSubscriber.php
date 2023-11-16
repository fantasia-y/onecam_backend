<?php

namespace App\EventSubscriber;

use App\Entity\Auth\User;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class JWTCreatedEventSubscriber implements EventSubscriberInterface
{
    public function onJWTCreated(JWTCreatedEvent $event)
    {
        $user = $event->getUser();
        if ($user instanceof User) {
            $payload = $event->getData();
            $payload['uuid'] = $user->getId();
            $event->setData($payload);
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'lexik_jwt_authentication.on_jwt_created' => 'onJWTCreated'
        ];
    }
}