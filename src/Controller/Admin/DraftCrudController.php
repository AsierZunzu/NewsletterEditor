<?php

namespace App\Controller\Admin;

use App\Entity\Draft;
use App\Entity\Newsletter;
use App\Enum\Roles;
use App\Exception\DraftPublishingException;
use App\Form\DraftPublishingType;
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
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

use function Symfony\Component\Translation\t;

class DraftCrudController extends AbstractCrudController
{
    public function __construct(
        private readonly AdminUrlGenerator $adminUrlGenerator,
    ) {
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular(t('Draft'))
            ->setEntityLabelInPlural(t('Drafts'))
            ->showEntityActionsInlined();
    }

    public static function getEntityFqcn(): string
    {
        return Draft::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('title', t('Title'));
        yield TextEditorField::new('content', t('Content'))
            ->hideOnIndex();
        yield AssociationField::new('createdBy', t('Created by'))
            ->setPermission(Roles::EDITOR->value)
            ->hideWhenCreating()
            ->setDisabled()
            ->setRequired(false);
    }

    public function configureActions(Actions $actions): Actions
    {
        $publishDraftAction = Action::new('publishDraftAction', t('Publish draft'), 'fa fa-upload')
            ->linkToCrudAction('publishDraftAction') /** @uses publishDraftAction */
            ->displayIf(
                fn (Draft $instance) => $instance->getCreatedBy()->getId() === $this->getUser()->getId()
            );
        $actions->add(Crud::PAGE_INDEX, $publishDraftAction);
        $actions->add(Crud::PAGE_DETAIL, $publishDraftAction);
        $actions->add(Crud::PAGE_INDEX, Action::DETAIL);
        $actions->update(Crud::PAGE_INDEX, Action::EDIT, function (Action $action) {
            return $action->displayIf(
                fn (Draft $instance) => $instance->getCreatedBy()->getId() === $this->getUser()->getId()
            );
        });
        $actions->update(Crud::PAGE_INDEX, Action::DELETE, function (Action $action) {
            return $action->displayIf(
                fn (Draft $instance) => $instance->getCreatedBy()->getId() === $this->getUser()->getId()
            );
        });
        $actions->disable(Action::BATCH_DELETE);
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

    public function edit(AdminContext $context)
    {
        $currentUser = $this->getUser();
        if (in_array(Roles::EDITOR->value, $currentUser->getRoles())) {
            return parent::detail($context);
        }
        $draft = $context->getEntity()->getInstance();
        if ($draft->getCreatedBy()->getId() !== $this->getUser()->getId()) {
            return $this->redirectToIndex();
        }
        return parent::edit($context);
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

    public function publishDraftAction(
        AdminContext $context,
        PublishingHelper $publishingHelper,
    ): Response {
        $draft = $context->getEntity()->getInstance();
        if ($draft->getCreatedBy()->getId() !== $this->getUser()->getId()) {
            return $this->redirectToIndex();
        }
        $form = $this->createForm(DraftPublishingType::class);
        $form->handleRequest($context->getRequest());
        if ($form->isSubmitted() && $form->isValid()) {
            $newsletter = $form->get(DraftPublishingType::FIELD_NEWSLETTER)->getData();
            try {
                $newsletterEntry = $publishingHelper->publishDraft($draft, $newsletter);
            } catch (DraftPublishingException $e) {
                $this->addFlash('danger', $e->getMessage());
                return $this->redirectToIndex();
            }
            $url = $this->adminUrlGenerator
                ->setController(NewsletterEntryCrudController::class)
                ->setEntityId($newsletterEntry->getId())
                ->setAction(Action::DETAIL)
                ->generateUrl();
            return $this->redirect($url);
        }

        return $this->render('draft/publish.html.twig', [ //TODO
            'form' => $form,
        ]);
    }

    private function redirectToIndex(): RedirectResponse
    {
        $url = $this->adminUrlGenerator
            ->setAction(Action::INDEX)
            ->generateUrl();
        return $this->redirect($url);
    }
}
