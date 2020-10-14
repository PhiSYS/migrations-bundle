<?php
declare(strict_types=1);

namespace DosFarma\MigrationsBundle;

use DosFarma\MigrationsBundle\DependencyInjection\Compiler\CheckCommandPass;
use DosFarma\MigrationsBundle\DependencyInjection\Compiler\DownCommandPass;
use DosFarma\MigrationsBundle\DependencyInjection\Compiler\GenerateCommandPass;
use DosFarma\MigrationsBundle\DependencyInjection\Compiler\InitCommandPass;
use DosFarma\MigrationsBundle\DependencyInjection\Compiler\MigrateCommandPass;
use DosFarma\MigrationsBundle\DependencyInjection\Compiler\RedoCommandPass;
use DosFarma\MigrationsBundle\DependencyInjection\Compiler\RollbackCommandPass;
use DosFarma\MigrationsBundle\DependencyInjection\Compiler\StatusCommandPass;
use DosFarma\MigrationsBundle\DependencyInjection\Compiler\UpCommandPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class DosFarmaMigrationsBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container
            ->addCompilerPass(new CheckCommandPass())
            ->addCompilerPass(new DownCommandPass())
            ->addCompilerPass(new GenerateCommandPass())
            ->addCompilerPass(new InitCommandPass())
            ->addCompilerPass(new MigrateCommandPass())
            ->addCompilerPass(new RedoCommandPass())
            ->addCompilerPass(new RollbackCommandPass())
            ->addCompilerPass(new StatusCommandPass())
            ->addCompilerPass(new UpCommandPass())
        ;
    }
}
