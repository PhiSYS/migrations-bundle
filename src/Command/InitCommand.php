<?php
declare(strict_types=1);

namespace PhiSYS\MigrationsBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class InitCommand extends AbstractCommand
{
    private const NAME = 'migrations:init';

    protected function configure()
    {
        parent::configure();

        $this->setName(self::NAME)
            ->setDescription('Initializes the migrations system')
            ->setHelp(<<<'EOT'
The <info>init</info> command creates the version control repository in the configured adapter

<info>migrations:init</info>

EOT
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->generateSchema();
    }

    protected function generateSchema(): void
    {
        $adapter = $this->configuration->adapter();
        if (!$adapter->hasSchema()) {
            $adapter->createSchema();
        }
    }
}
