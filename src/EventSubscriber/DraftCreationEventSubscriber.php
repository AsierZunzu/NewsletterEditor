<?php

namespace App\EventSubscriber;

use App\Entity\Draft;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class DraftCreationEventSubscriber implements EventSubscriberInterface
{
    public function __construct(private readonly Security $security)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            BeforeEntityPersistedEvent::class => ['setDraftCreator'], /** @uses setDraftCreator */
        ];
    }

    public function setDraftCreator(BeforeEntityPersistedEvent $event): void
    {
        $entity = $event->getEntityInstance();
        if (!($entity instanceof Draft)) {
            return;
        }
        $createdBy = $this->security->getUser();
        $entity->setCreatedBy($createdBy);
    }
}
