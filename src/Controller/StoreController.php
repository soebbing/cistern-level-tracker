<?php declare(strict_types=1);

namespace App\Controller;

use App\Repository\LevelRepository;
use DateTime;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/add/{liter}/{date}", name="add", methods={"GET"})
 * @ParamConverter("date", options={"format": "Y-m-d h:i:s"})
 */
class StoreController
{
    private LevelRepository $cisternRepository;

    public function __construct(LevelRepository $cisternRepository)
    {
        $this->cisternRepository = $cisternRepository;
    }

    public function __invoke(float $liter, DateTime $date = null): JsonResponse
    {
        if (!$date) {
            $date = new DateTime();
        }

        $this->cisternRepository->addEntry($liter, $date);

        return new JsonResponse([
            'liter' => $liter,
            'date' => $date,
        ]);
    }
}
