<?php
declare(strict_types=1);

namespace DosFarma\MigrationsBundle\DependencyInjection\Compiler;

use DosFarma\MigrationsBundle\Command\GenerateCommand;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class GenerateCommandPass extends AbstractCommandPass
{
    public function process(ContainerBuilder $container): void
    {
        $this->addCommandDefinition(GenerateCommand::class, $container);
    }
}
