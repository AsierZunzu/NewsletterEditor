<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Enum\Roles;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public const COMMON_USER_REFERENCE = 'common-user';
    public const ADMIN_USER_REFERENCE = 'admin-user';
    public const EDITOR_USER_REFERENCE = 'editor-user';

    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher,
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $user = $this->generateCommonUser();
        $manager->persist($user);
        $this->addReference(self::COMMON_USER_REFERENCE, $user);
        $user = $this->generateAdminUser();
        $this->addReference(self::ADMIN_USER_REFERENCE, $user);
        $manager->persist($user);
        $user = $this->generateEditorUser();
        $this->addReference(self::EDITOR_USER_REFERENCE, $user);
        $manager->persist($user);
        $manager->flush();
    }

    private function generateCommonUser(): User
    {
        $user = new User();
        $user->setUsername('testUser');
        $user->setEmail('test@mail.com');
        $plaintextPassword = 'pwgen123';
        $hashedPassword = $this->passwordHasher->hashPassword(
            $user,
            $plaintextPassword,
        );
        $user->setRoles([]);
        $user->setPassword($hashedPassword);
        return $user;
    }

    private function generateAdminUser(): User
    {
        $user = new User();
        $user->setUsername('adminUser');
        $user->setEmail('admin@mail.com');
        $plaintextPassword = 'pwgen123';
        $hashedPassword = $this->passwordHasher->hashPassword(
            $user,
            $plaintextPassword,
        );
        $user->setRoles([Roles::ADMIN->value]);
        $user->setPassword($hashedPassword);
        return $user;
    }

    private function generateEditorUser(): User
    {
        $user = new User();
        $user->setUsername('editorUser');
        $user->setEmail('editor@mail.com');
        $plaintextPassword = 'pwgen123';
        $hashedPassword = $this->passwordHasher->hashPassword(
            $user,
            $plaintextPassword,
        );
        $user->setRoles([Roles::EDITOR->value]);
        $user->setPassword($hashedPassword);
        return $user;
    }
}
