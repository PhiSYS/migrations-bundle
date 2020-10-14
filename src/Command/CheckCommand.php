<?php
declare(strict_types=1);

namespace DosFarma\MigrationsBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CheckCommand extends AbstractCommand
{
    private const NAME = 'migrations:check';

    protected function configure()
    {
        parent::configure();

        $this->setName(self::NAME)
            ->setDescription('Check all migrations have been run, exit with non-zero if not')
            ->setHelp(<<<'EOT'
The <info>check</info> checks that all migrations have been run and exits with a 
non-zero exit code if not, useful for build or deployment scripts.

<info>migrations:check</info>

EOT
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $migrationsInDirectory = $this->getMigrationsInDirectory($output);
        $versionsAlreadyDone = $this->getVersionsAlreadyDone();
        $down = array();

        foreach($migrationsInDirectory as $migration) {
            if (!in_array($migration->getVersion(), $versionsAlreadyDone)) {
                $down[] = $migration;
            }
        }

        if (empty($down)) {
            return 0;
        }

        $output->writeln("");
        $output->writeln(" Status   Migration ID    Migration Name ");
        $output->writeln("-----------------------------------------");

        foreach ($down as $migration) {
            $output->writeln(
                \sprintf(
                    "   <error>down</error>  %14s  <comment>%s</comment>",
                    $migration->getVersion(),
                    $migration->getName(),
                )
            );
        }

        $output->writeln("");

        return 1;
    }
}
