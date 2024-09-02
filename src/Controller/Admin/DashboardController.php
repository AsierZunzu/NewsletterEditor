<?php

namespace App\Controller\Admin;

use App\Entity\CalendarEvent;
use App\Entity\Draft;
use App\Entity\Newsletter;
use App\Entity\NewsletterEntry;
use App\Entity\User;
use App\Enum\Roles;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

use function Symfony\Component\Translation\t;

class DashboardController extends AbstractDashboardController
{
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        if (in_array(Roles::ADMIN->value, $this->getUser()->getRoles())) {
            return $this->redirect($adminUrlGenerator->setController(AdminUserCrudController::class)->generateUrl());
        }
        if (in_array(Roles::EDITOR->value, $this->getUser()->getRoles())) {
            return $this->redirect($adminUrlGenerator->setController(NewsletterCrudController::class)->generateUrl());
        }
        return $this->redirect($adminUrlGenerator->setController(DraftCrudController::class)->generateUrl());
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Newsletter editor')
            ->setLocales([
                'es' => t('Spanish'),
                'en' => t('English'),
            ])
//            ->setFaviconPath('favicon.svg')
        ;
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToCrud(t('Drafts'), 'fas fa-pen', Draft::class);
        yield MenuItem::linkToCrud(t('Newsletter entries'), 'fas fa-note-sticky', NewsletterEntry::class);
        yield MenuItem::linkToCrud(t('Newsletter'), 'fas fa-newspaper', Newsletter::class)
            ->setPermission(Roles::EDITOR->value);
        yield MenuItem::linkToCrud(t('Users'), 'fas fa-user', User::class)
            ->setController(AdminUserCrudController::class)
            ->setPermission(Roles::ADMIN->value);
    }
}
