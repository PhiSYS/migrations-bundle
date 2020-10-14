<?php
declare(strict_types=1);

namespace DosFarma\MigrationsBundle\DependencyInjection\Compiler;

use DosFarma\MigrationsBundle\Command\InitCommand;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class InitCommandPass extends AbstractCommandPass
{
    public function process(ContainerBuilder $container): void
    {
        $this->addCommandDefinition(InitCommand::class, $container);
    }
}
