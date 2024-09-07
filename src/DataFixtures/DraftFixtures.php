<?php

namespace App\DataFixtures;

use App\Entity\Draft;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class DraftFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $draft = $this->generateNewsletterForCommonUser();
        $manager->persist($draft);
        $draft = $this->generateNewsletterForEditorUser();
        $manager->persist($draft);
        $manager->flush();
    }

    private function generateNewsletterForCommonUser(): Draft
    {
        $newsletter = new Draft();
        $newsletter->setTitle('A simple story from a common user');
        $newsletter->setContent('Here comes a simple story from a common user');
        $newsletter->setCreatedBy($this->getReference(UserFixtures::COMMON_USER_REFERENCE));
        return $newsletter;
    }

    private function generateNewsletterForEditorUser(): Draft
    {
        $draft = new Draft();
        $draft->setTitle('The editor is speaking now');
        $draft->setContent('Everybody listen please');
        $draft->setCreatedBy($this->getReference(UserFixtures::EDITOR_USER_REFERENCE));
        return $draft;
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
        ];
    }
}
