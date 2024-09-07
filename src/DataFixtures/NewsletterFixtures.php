<?php

namespace App\DataFixtures;

use App\Entity\Newsletter;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class NewsletterFixtures extends Fixture
{
    public const NOT_SENT_NEWSLETTER_REFERENCE = 'not_sent_newsletter';
    public const SENT_NEWSLETTER_REFERENCE = 'sent_newsletter';

    public function load(ObjectManager $manager): void
    {
        $newsletter = $this->generateNotSentNewsletter();
        $this->addReference(self::NOT_SENT_NEWSLETTER_REFERENCE, $newsletter);
        $manager->persist($newsletter);
        $newsletter = $this->generateSentNewsletter();
        $this->addReference(self::SENT_NEWSLETTER_REFERENCE, $newsletter);
        $manager->persist($newsletter);
        $manager->flush();
    }

    private function generateNotSentNewsletter(): Newsletter
    {
        $newsletter = new Newsletter();
        $newsletter->setTitle('Example 1');
        $newsletter->setSent(false);
        return $newsletter;
    }

    private function generateSentNewsletter(): Newsletter
    {
        $newsletter = new Newsletter();
        $newsletter->setTitle('Example 2');
        $newsletter->setSent(true);
        return $newsletter;
    }
}
