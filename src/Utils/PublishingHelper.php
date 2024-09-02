<?php

namespace App\Utils;

use App\Entity\Draft;
use App\Entity\Newsletter;
use App\Entity\NewsletterEntry;
use App\Exception\DraftPublishingException;
use App\Exception\EntryUnpublishingException;
use Doctrine\ORM\EntityManagerInterface;

class PublishingHelper
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    /**
     * @throws DraftPublishingException
     */
    public function publishDraft(Draft $draft, Newsletter $newsletter): NewsletterEntry
    {
        if ($newsletter->isSent()) {
            throw new DraftPublishingException("Newsletter {$newsletter->getId()} has already been sent");
        }
        $newsletterEntry = new NewsletterEntry();
        $newsletterEntry->setTitle($draft->getTitle());
        $newsletterEntry->setCreatedBy($draft->getCreatedBy());
        $newsletterEntry->setContent($draft->getContent());
        $newsletterEntry->setNewsletter($newsletter);
        $this->em->persist($newsletterEntry);
        $this->em->remove($draft);
        $this->em->flush();
        return $newsletterEntry;
    }

    /**
     * @throws EntryUnpublishingException
     */
    public function unpublishEntry(NewsletterEntry $newsletterEntry): Draft
    {
        $newsletter = $newsletterEntry->getNewsletter();
        if ($newsletter->isSent()) {
            throw new EntryUnpublishingException("Newsletter {$newsletter->getId()} has already been sent");
        }
        $draft = new Draft();
        $draft->setTitle($newsletterEntry->getTitle());
        $draft->setCreatedBy($newsletterEntry->getCreatedBy());
        $draft->setContent($newsletterEntry->getContent());
        $this->em->persist($draft);
        $this->em->remove($newsletterEntry);
        $this->em->flush();
        return $draft;

    }
}
