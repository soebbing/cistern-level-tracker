<?php declare(strict_types=1);

namespace App\Controller;

use App\Repository\LevelRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

/**
 * @Route("/", name="home", methods={"GET"})
 */
class GraphController
{
    private Environment $twig;

    private LevelRepository $cisternRepository;

    public function __construct(Environment $twig, LevelRepository $cisternRepository)
    {
        $this->twig = $twig;
        $this->cisternRepository = $cisternRepository;
    }

    public function __invoke()
    {
        return new Response(
            $this->twig->render(
                'base.html.twig',
                [
                    'levels' => $this->cisternRepository->getChartData(),
                ]
            ),
        );
    }
}
