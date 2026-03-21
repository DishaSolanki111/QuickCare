<?php
declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use function PHPMaker2026\Project2\Config;

return App::config([
    'flysystem' => Config('FLYSYSTEM'),
]);
