<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController
{
    /**
     * @Route("/", name="home", methods={"GET"})
     */
    public function __invoke(): RedirectResponse
    {
        return new RedirectResponse('/admin', Response::HTTP_FOUND);
    }
}
