<?php
declare(strict_types=1);

namespace App\Controller;

use App\Repository\LevelRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

/**
 * @Route("/export", name="export", methods={"GET"})
 */
class ExportController
{
    private Environment $twig;

    private LevelRepository $cisternRepository;

    public function __construct(Environment $twig, LevelRepository $cisternRepository)
    {
        $this->twig = $twig;
        $this->cisternRepository = $cisternRepository;
    }

    public function __invoke(Request $request): Response
    {
        $response = new Response(
            $this->twig->render('export/template.csv.twig',
            [
                'delimiter' => $request->query->get('delimiter', "\t"),
                'levels' => $this->cisternRepository->getAllResults(),
            ]
        ));

        $disposition = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            'cistern-level-data.csv'
        );
        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', $disposition);

        return $response;
    }
}
