<?php
declare(strict_types=1);

namespace PhiSYS\MigrationsBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class StatusCommand extends AbstractCommand
{
    private const NAME = 'migrations:status';

    protected function configure()
    {
        parent::configure();

        $this->setName(self::NAME)
            ->setDescription('Show the up/down status of all migrations')
            ->setHelp(<<<'EOT'
The <info>status</info> command prints a list of all migrations, along with their current status 

<info>migrations:status</info>

EOT
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $migrationsInDirectory = $this->getMigrationsInDirectory($output);
        $output->writeln(PHP_EOL . " Status   Migration ID    Migration Name ");
        $output->writeln("-------------------------------------------------------");

        $versionsAlreadyDone = $this->getVersionsAlreadyDone();
        foreach($migrationsInDirectory as $migration) {

            if (in_array($migration->getVersion(), $versionsAlreadyDone)) {
                $status = "     <info>up</info> ";
                unset($versionsAlreadyDone[array_search($migration->getVersion(), $versionsAlreadyDone)]);
            } else {
                $status = "   <error>down</error> ";
            }

            $output->writeln(
                sprintf("%s %14s  <comment>%s</comment>", $status, $migration->getVersion(), $migration->getName()),
            );
        }

        foreach($versionsAlreadyDone as $missing) {
            $output->writeln(sprintf("   <error>up</error>  %14s  <error>** MISSING **</error> ", $missing));
        }

        // print status
        $output->writeln("");

        return 0;
    }
}
