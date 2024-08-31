<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Enum\Roles;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use function Symfony\Component\Translation\t;

class AdminUserCrudController extends AbstractCrudController
{
    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityPermission(Roles::ADMIN->value);
    }

    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('username', t('Username')),
            EmailField::new('email', t('Email')),
            ChoiceField::new('roles', t('Roles'))
                ->allowMultipleChoices()
                ->setFormTypeOption('choices', Roles::cases())
                ->setFormTypeOption('choice_label', function (Roles $choice) {
                    return $choice->name;
                })
                ->setFormTypeOption('choice_value', function (Roles|int|string|null $value): ?string {
                    if (null === $value) {
                        return null;
                    }

                    return (string) ($value instanceof Roles ? $value->value : $value);
                }),
        ];
    }
}
