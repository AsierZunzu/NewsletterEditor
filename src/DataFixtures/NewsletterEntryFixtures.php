<?php

namespace App\DataFixtures;

use App\Entity\NewsletterEntry;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class NewsletterEntryFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $newsletterEntry = $this->generateNewsletterEntryOnSentNewsletter();
        $manager->persist($newsletterEntry);
        $newsletterEntry = $this->generateNewsletterEntryOnNotSentNewsletter();
        $manager->persist($newsletterEntry);
        $newsletterEntry = $this->generateNewsletterEntryOnNotSentNewsletter2();
        $manager->persist($newsletterEntry);
        $manager->flush();
    }

    private function generateNewsletterEntryOnSentNewsletter(): NewsletterEntry
    {
        $newsletterEntry = new NewsletterEntry();
        $newsletterEntry->setTitle('Published entry');
        $newsletterEntry->setContent('some interesting story here');
        $newsletterEntry->setCreatedBy($this->getReference(UserFixtures::COMMON_USER_REFERENCE));
        $newsletterEntry->setNewsletter($this->getReference(NewsletterFixtures::SENT_NEWSLETTER_REFERENCE));
        return $newsletterEntry;
    }

    private function generateNewsletterEntryOnNotSentNewsletter(): NewsletterEntry
    {
        $newsletterEntry = new NewsletterEntry();
        $newsletterEntry->setTitle('Short entry');
        $newsletterEntry->setContent('Nothing special here');
        $newsletterEntry->setCreatedBy($this->getReference(UserFixtures::EDITOR_USER_REFERENCE));
        $newsletterEntry->setNewsletter($this->getReference(NewsletterFixtures::NOT_SENT_NEWSLETTER_REFERENCE));
        return $newsletterEntry;
    }

    private function generateNewsletterEntryOnNotSentNewsletter2(): NewsletterEntry
    {
        $newsletterEntry = new NewsletterEntry();
        $newsletterEntry->setTitle('Long entry');
        $newsletterEntry->setContent('<div><h1>Hello</h1><div><strong>Lorem ipsum</strong> dolor sit amet, <em>consectetur </em>adipiscing elit, <del>sed </del>do eiusmod tempor incididunt ut labore et dolore magna <a href="https://fontawesome.com/icons/paper-plane?f=classic&amp;s=solid">aliqua</a>. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</div><blockquote>so...</blockquote><pre>code</pre><div><br></div><ul><li>and</li><li>a</li><li>list<ul><li>with</li><li>an</li><li>indent</li></ul></li></ul><div><br></div><ol><li>and</li><li>another</li><li>list</li></ol></div>');
        $newsletterEntry->setCreatedBy($this->getReference(UserFixtures::COMMON_USER_REFERENCE));
        $newsletterEntry->setNewsletter($this->getReference(NewsletterFixtures::NOT_SENT_NEWSLETTER_REFERENCE));
        return $newsletterEntry;
    }

    public function getDependencies(): array
    {
        return [
            NewsletterFixtures::class,
            UserFixtures::class,
        ];
    }
}
