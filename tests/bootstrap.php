<?php
declare(strict_types=1);

if (\file_exists(__DIR__ . '/bootstrap.php.cache')) {
    require_once __DIR__ . '/bootstrap.php.cache';
}

if (\file_exists(__DIR__ . '/AppKernel.php')) {
    require_once __DIR__ . '/AppKernel.php';
}
require_once __DIR__ . '/../vendor/autoload.php';

use App\Kernel as AppKernel;
use Symfony\Bundle\FrameworkBundle\Console\Application;

$kernel = new AppKernel('test', true); // create a "test" kernel
$kernel->boot();

$application = new Application($kernel);
