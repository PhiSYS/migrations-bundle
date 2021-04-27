<?php
declare(strict_types=1);

namespace PhiSYS\MigrationsBundle\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DownCommand extends AbstractCommand
{
    private const NAME = 'migrations:down';

    protected function configure()
    {
        parent::configure();

        $this->setName(self::NAME)
            ->addArgument('version', InputArgument::REQUIRED, 'The version number for the migration')
            ->setDescription('Revert a specific migration')
            ->setHelp(<<<'EOT'
The <info>down</info> command reverts a specific migration

<info>migrations:down 20111018185412</info>

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
            $output->writeln("Migration <error>was not up</error>. Nothing to do.");

            return 1;
        }

        $this->getMigrator($output)->down($migrationsInDirectory[$targetVersion]);

        return 0;
    }
}
