<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Level;
use App\Repository\LevelRepository;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

class DashboardController extends AbstractDashboardController
{
    private Environment $twig;

    private LevelRepository $cisternRepository;

    public function __construct(Environment $twig, LevelRepository $cisternRepository)
    {
        $this->twig = $twig;
        $this->cisternRepository = $cisternRepository;
    }

    /**
     * @Route("/admin")
     */
    public function index(): Response
    {
        $since = (new \DateTimeImmutable('now', new \DateTimeZone('UTC')))
            ->sub(new \DateInterval('P1Y'))
        ;

        return new Response(
            $this->twig->render(
                'graph/template.html.twig',
                [
                    'levels' => $this->cisternRepository->getDataSince($since),
                ]
            ),
        );
    }

    public function configureMenuItems(): iterable
    {
        return [
            MenuItem::linkToDashboard('Dashboard', 'fa fa-home'),

            MenuItem::section('Level'),
            MenuItem::linkToCrud('Levels', 'fa fa-water', Level::class),

            MenuItem::section('Export'),
            MenuItem::linkToRoute('Export as CSV', 'fa fa-download', 'export', []),
        ];
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            // the name visible to end users
            // you can include HTML contents too (e.g. to link to an image)
            ->setTitle('Cistern Level Tracker')

            // the path defined in this method is passed to the Twig asset() function
            ->setFaviconPath('favicon.svg')

            // the domain used by default is 'messages'
            ->setTranslationDomain('my-custom-domain')

            // there's no need to define the "text direction" explicitly because
            // its default value is inferred dynamically from the user locale
            ->setTextDirection('ltr')

            // set this option if you prefer the page content to span the entire
            // browser width, instead of the default design which sets a max width
            ->renderContentMaximized()

            // set this option if you prefer the sidebar (which contains the main menu)
            // to be displayed as a narrow column instead of the default expanded design
            ->renderSidebarMinimized()

            // by default, all backend URLs include a signature hash. If a user changes any
            // query parameter (to "hack" the backend) the signature won't match and EasyAdmin
            // triggers an error. If this causes any issue in your backend, call this method
            // to disable this feature and remove all URL signature checks
            ->disableUrlSignatures()
            ;
    }
}
