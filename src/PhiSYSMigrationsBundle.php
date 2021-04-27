<?php
declare(strict_types=1);

namespace PhiSYS\MigrationsBundle;

use PhiSYS\MigrationsBundle\DependencyInjection\Compiler\CheckCommandPass;
use PhiSYS\MigrationsBundle\DependencyInjection\Compiler\DownCommandPass;
use PhiSYS\MigrationsBundle\DependencyInjection\Compiler\GenerateCommandPass;
use PhiSYS\MigrationsBundle\DependencyInjection\Compiler\InitCommandPass;
use PhiSYS\MigrationsBundle\DependencyInjection\Compiler\MigrateCommandPass;
use PhiSYS\MigrationsBundle\DependencyInjection\Compiler\RedoCommandPass;
use PhiSYS\MigrationsBundle\DependencyInjection\Compiler\RollbackCommandPass;
use PhiSYS\MigrationsBundle\DependencyInjection\Compiler\StatusCommandPass;
use PhiSYS\MigrationsBundle\DependencyInjection\Compiler\UpCommandPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class PhiSYSMigrationsBundle extends Bundle
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
