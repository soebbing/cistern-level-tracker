<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Level;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class LevelController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Level::class;
    }
}
