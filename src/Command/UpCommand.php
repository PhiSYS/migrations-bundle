<?php
declare(strict_types=1);

namespace PhiSYS\MigrationsBundle\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UpCommand extends AbstractCommand
{
    private const NAME = 'migrations:up';

    protected function configure()
    {
        parent::configure();

        $this->setName(self::NAME)
            ->addArgument('version', InputArgument::REQUIRED, 'The version number for the migration')
            ->setDescription('Run a specific migration')
            ->setHelp(<<<'EOT'
The <info>up</info> command runs a specific migration

<info>migrations:up 20111018185121</info>

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

        if (in_array($targetVersion, $versionsAlreadyDone)) {
            $output->writeln("Migration is <info>already up</info>. Use migrations:redo to perform a down and then up.");

            return 0;
        }

        if (!isset($migrationsInDirectory[$targetVersion])) {
            $output->writeln("Migration <error>not found</error>.");

            return 1;
        }

        $this->getMigrator($output)->up($migrationsInDirectory[$targetVersion]);

        return 0;
    }
}
