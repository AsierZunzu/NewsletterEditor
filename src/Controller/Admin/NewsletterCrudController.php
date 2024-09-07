<?php

namespace App\Controller\Admin;

use App\Entity\Newsletter;
use App\Enum\Roles;
use App\Utils\NewsletterSenderUtils;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;

use function Symfony\Component\Translation\t;

class NewsletterCrudController extends AbstractCrudController
{
    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular(t('Newsletter'))
            ->setEntityLabelInPlural(t('Newsletters'))
            ->setEntityPermission(Roles::EDITOR->value)
            ->showEntityActionsInlined();
    }

    public static function getEntityFqcn(): string
    {
        return Newsletter::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', t('ID'))
            ->setDisabled();
        yield TextField::new('title', t('Title'));
        yield BooleanField::new('sent', t('Sent'))
            ->renderAsSwitch(false);
        yield AssociationField::new('entries', t('Entries'))
            ->setDisabled();
    }

    public function configureActions(Actions $actions): Actions
    {
        $preview = Action::new('previewAction', t('Preview newsletter'), 'fa fa-eye')
            ->linkToCrudAction('previewAction'); /** @uses previewAction */
        $publishNow = Action::new('sendNowAction', t('Send now'), 'fa fa-paper-plane')
            ->linkToCrudAction('sendNowAction') /** @uses sendNowAction */
            ->displayIf(static function (Newsletter $instance) {
                return !$instance->isSent();
            });
        $actions->add(Crud::PAGE_INDEX, $preview);
        $actions->add(Crud::PAGE_DETAIL, $preview);
        $actions->add(Crud::PAGE_INDEX, $publishNow);
        $actions->add(Crud::PAGE_DETAIL, $publishNow);
        $actions->add(Crud::PAGE_EDIT, Action::INDEX);
        return $actions;
    }

    public function previewAction(
        AdminContext $context,
    ): Response {
        /** @var Newsletter $newsletter */
        $newsletter = $context->getEntity()->getInstance();
        return $this->render('newsletter/newsletter.html.twig', [
            'newsletter' => $newsletter,
        ]);
    }

    public function sendNowAction(
        AdminContext $context,
        AdminUrlGenerator $adminUrlGenerator,
        NewsletterSenderUtils $newsletterSenderUtils,
    ): Response {
        /** @var Newsletter $newsletter */
        $newsletter = $context->getEntity()->getInstance();
        $newsletterSenderUtils->send($newsletter);
        return $this->redirect($adminUrlGenerator->setAction(Action::INDEX)->generateUrl());

    }
}
