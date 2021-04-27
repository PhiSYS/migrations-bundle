<?php
declare(strict_types=1);

namespace PhiSYS\MigrationsBundle\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MigrateCommand extends AbstractCommand
{
    private const NAME = 'migrations:migrate';

    protected function configure()
    {
        parent::configure();

        $this->setName(self::NAME)
            ->addOption('--target', '-t', InputArgument::OPTIONAL, 'The version number to migrate to')
            ->setDescription('Run all migrations')
            ->setHelp(<<<'EOT'
The <info>migrate</info> command runs all available migrations, optionally up to a specific version

<info>migrations:migrate</info>
<info>migrations:migrate -t 20111018185412</info>

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

        $current = 0;
        if (!empty($versionsAlreadyDone)) {
            // Get the last run migration number
            $current = end($versionsAlreadyDone);
        }

        if (null !== $targetVersion && 0 != $targetVersion && !isset($migrationsInDirectory[$targetVersion])) {
            return;
        }

        $versionNumbers = array_merge($versionsAlreadyDone, array_keys($migrationsInDirectory));

        if (empty($versionNumbers)) {
            return;
        }

        $targetVersion = max($versionNumbers);
        $direction = $targetVersion > $current ? 'up' : 'down';

        if ($direction == 'down') {
            /**
             * Run downs first
             */
            krsort($migrationsInDirectory);
            foreach($migrationsInDirectory as $migration) {
                if ($migration->getVersion() <= $targetVersion) {
                    break;
                }

                if (in_array($migration->getVersion(), $versionsAlreadyDone)) {
                    $this->getMigrator($output)->down($migration);
                }
            }
        }

        ksort($migrationsInDirectory);
        foreach($migrationsInDirectory as $migration) {
            if ($migration->getVersion() > $targetVersion) {
                break;
            }

            if (!in_array($migration->getVersion(), $versionsAlreadyDone)) {
                $this->getMigrator($output)->up($migration);
            }
        }

        return 0;
    }
}
