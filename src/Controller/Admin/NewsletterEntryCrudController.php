<?php

namespace App\Controller\Admin;

use App\Entity\NewsletterEntry;
use App\Enum\Roles;
use App\Exception\EntryUnpublishingException;
use App\Utils\PublishingHelper;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Orm\EntityRepository;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

use function Symfony\Component\Translation\t;

class NewsletterEntryCrudController extends AbstractCrudController
{
    public function __construct(
        private readonly AdminUrlGenerator $adminUrlGenerator,
    ) {
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular(t('Newsletter entry'))
            ->setEntityLabelInPlural(t('Newsletter entries'))
            ->showEntityActionsInlined();
    }

    public static function getEntityFqcn(): string
    {
        return NewsletterEntry::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('title', t('Title'))
            ->setDisabled();
        yield TextEditorField::new('content', t('Content'))
            ->setDisabled()
            ->hideOnIndex();
        yield AssociationField::new('createdBy', t('Created by'))
            ->setPermission(Roles::EDITOR->value)
            ->setDisabled()
            ->setRequired(false);
    }

    public function configureActions(Actions $actions): Actions
    {
        $unpublishDraft = Action::new('unpublishAction', t('Unpublish entry'), 'fa fa-upload')
            ->linkToCrudAction('unpublishAction') /** @uses unpublishAction */
            ->displayIf(
                fn (NewsletterEntry $instance) =>
                    ($instance->getCreatedBy()->getId() === $this->getUser()->getId())
                    && (!$instance->getNewsletter()->isSent())
            );
        $actions->add(Crud::PAGE_INDEX, $unpublishDraft);
        $actions->add(Crud::PAGE_DETAIL, $unpublishDraft);
        $actions->add(Crud::PAGE_INDEX, Action::DETAIL);
        $actions->update(Crud::PAGE_INDEX, Action::DELETE, function (Action $action) {
            return $action->displayIf(
                fn (NewsletterEntry $instance) =>
                    ($instance->getCreatedBy()->getId() === $this->getUser()->getId())
                    && (!$instance->getNewsletter()->isSent())
            );
        });
        $actions->disable(Action::EDIT, Action::BATCH_DELETE);
        $actions->add(Crud::PAGE_EDIT, Action::INDEX);
        return $actions;
    }

    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        $currentUser = $this->getUser();
        $queryBuilder = parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters);
        if (in_array(Roles::EDITOR->value, $currentUser->getRoles())) {
            return $queryBuilder;
        }
        $queryBuilder->andWhere('entity.createdBy = :currentUser');
        $queryBuilder->setParameter('currentUser', $currentUser);
        return $queryBuilder;
    }

    public function detail(AdminContext $context)
    {
        $currentUser = $this->getUser();
        if (in_array(Roles::EDITOR->value, $currentUser->getRoles())) {
            return parent::detail($context);
        }
        $draft = $context->getEntity()->getInstance();
        if ($draft->getCreatedBy()->getId() !== $this->getUser()->getId()) {
            return $this->redirectToIndex();
        }
        return parent::detail($context);
    }

    public function unpublishAction(
        AdminContext $context,
        PublishingHelper $publishingHelper,
    ): Response {
        /** @var NewsletterEntry $newsletterEntry */
        $newsletterEntry = $context->getEntity()->getInstance();
        if ($newsletterEntry->getCreatedBy()->getId() !== $this->getUser()->getId()) {
            return $this->redirectToIndex();
        }
        try {
            $draft = $publishingHelper->unpublishEntry($newsletterEntry);
        } catch (EntryUnpublishingException $e) {
            $this->addFlash('danger', $e->getMessage());
            return $this->redirectToIndex();
        }
        $url = $this->adminUrlGenerator
            ->setController(DraftCrudController::class)
            ->setEntityId($draft->getId())
            ->setAction(Action::EDIT)
            ->generateUrl();
        return $this->redirect($url);
    }

    private function redirectToIndex(): RedirectResponse
    {
        $url = $this->adminUrlGenerator
            ->setAction(Action::INDEX)
            ->generateUrl();
        return $this->redirect($url);
    }
}
