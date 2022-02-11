<?php

declare(strict_types=1);

/*
 * This file is part of PHP CS Fixer.
 * (c) Fabien Potencier <fabien@symfony.com>
 *     Dariusz Rumiński <dariusz.ruminski@gmail.com>
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

if (file_exists(__DIR__.'/bootstrap.php.cache')) {
    require_once __DIR__.'/bootstrap.php.cache';
}

if (file_exists(__DIR__.'/AppKernel.php')) {
    require_once __DIR__.'/AppKernel.php';
}

require_once __DIR__.'/../vendor/autoload.php';

use App\Kernel as AppKernel;
use Symfony\Bundle\FrameworkBundle\Console\Application;

$kernel = new AppKernel('test', true); // create a "test" kernel
$kernel->boot();

$application = new Application($kernel);
