<?php

namespace App\DataFixtures;

use App\Entity\Newsletter;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class NewsletterFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $newsletter = $this->generateNotSentNewsletter();
        $manager->persist($newsletter);
        $newsletter = $this->generateSentNewsletter();
        $manager->persist($newsletter);
        $manager->flush();
    }

    private function generateNotSentNewsletter(): Newsletter
    {
        $newsletter = new Newsletter();
        $newsletter->setSent(false);
        return $newsletter;
    }

    private function generateSentNewsletter(): Newsletter
    {
        $newsletter = new Newsletter();
        $newsletter->setSent(true);
        return $newsletter;
    }
}
