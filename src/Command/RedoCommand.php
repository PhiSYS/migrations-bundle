<?php
declare(strict_types=1);

namespace DosFarma\MigrationsBundle\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RedoCommand extends AbstractCommand
{
    private const NAME = 'migrations:redo';

    protected function configure()
    {
        parent::configure();

        $this->setName(self::NAME)
            ->addArgument('version', InputArgument::REQUIRED, 'The version number for the migration')
            ->setDescription('Redo a specific migration')
            ->setHelp(<<<'EOT'
The <info>redo</info> command redo a specific migration

<info>migrations:redo 20111018185412</info>

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
        $targetVersion = $input->getArgument('version');

        if (!isset($migrationsInDirectory[$targetVersion])) {
            $output->writeln("Migration <error>not found</error>.");

            return 1;
        }

        if (!in_array($targetVersion, $versionsAlreadyDone)) {
            $output->writeln("Migration is <info>already down</info>. Use migrations:up.");

            return 1;
        }

        $migrator = $this->getMigrator($output);
        $migrator->down($migrationsInDirectory[$targetVersion]);
        $migrator->up($migrationsInDirectory[$targetVersion]);

        return 0;
    }
}
