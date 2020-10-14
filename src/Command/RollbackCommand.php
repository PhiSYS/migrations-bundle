<?php
declare(strict_types=1);

namespace DosFarma\MigrationsBundle\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RollbackCommand extends AbstractCommand
{
    private const NAME = 'migrations:rollback';

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        parent::configure();

        $this->setName(self::NAME)
            ->addOption('--target', '-t', InputArgument::OPTIONAL, 'The version number to rollback to')
            ->setDescription('Rollback last, or to a specific migration')
            ->setHelp(<<<'EOT'
The <info>rollback</info> command reverts the last migration, or optionally up to a specific version

<info>migrations:rollback</info>
<info>migrations:rollback -t 20111018185412</info>

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
        $targetVersion = $input->getOption('target');

        // Check we have at least 1 migration to revert
        if (empty($versionsAlreadyDone) || $targetVersion == end($versionsAlreadyDone)) {
            $output->writeln("<error>No migrations to rollback</error>");
            return 0;
        }

        $targetVersion = $this->realTargetVersion($targetVersion, $versionsAlreadyDone);

        // Check the target version exists
        if (0 !== $targetVersion && !isset($migrationsInDirectory[$targetVersion])) {
            $output->writeln("<error>Target version ($targetVersion) not found</error>");
            return 0;
        }

        // Revert the migration(s)
        krsort($migrationsInDirectory);
        $migrator = $this->getMigrator($output);
        foreach($migrationsInDirectory as $migration) {
            if ($migration->getVersion() <= $targetVersion) {
                break;
            }

            if (in_array($migration->getVersion(), $versionsAlreadyDone)) {
                $migrator->down($migration);
            }
        }

        return 0;
    }

    private function realTargetVersion($targetVersion, &$versionsAlreadyDone)
    {
        // If no target version was supplied, revert the last migration
        if (null === $targetVersion) {
            // Get the migration before the last run migration
            $previousVersion = count($versionsAlreadyDone) - 2;
            $targetVersion = $previousVersion >= 0 ? $versionsAlreadyDone[$previousVersion] : 0;
        } else {
            // Get the first migration number
            $first = reset($versionsAlreadyDone);

            // If the target version is before the first migration, revert all migrations
            if ($targetVersion < $first) {
                $targetVersion = 0;
            }
        }

        return $targetVersion;
    }
}
