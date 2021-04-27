<?php
declare(strict_types=1);

namespace PhiSYS\MigrationsBundle\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateCommand extends AbstractCommand
{
    private const NAME = 'migrations:generate';

    protected function configure()
    {
        parent::configure();

        $this->setName(self::NAME)
            ->addArgument('name', InputArgument::REQUIRED, 'The name for the migration')
            ->setDescription('Generate a new migration')
            ->setHelp(<<<'EOT'
The <info>generate</info> command creates a new migration with the specified name

<info>migrations:generate MyFeatureName</info>

EOT
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $migrationName = $this->validateMigrationName($input->getArgument('name'));
        $correlation = date('YmdHis');
        $className = $this->validateClassName($migrationName . "_$correlation");
        $migrationFilePath =
            $this->configuration->migrationsDirectory() . DIRECTORY_SEPARATOR . $correlation . "_$migrationName.php";

        $this->assertMigrationFileDoesNotAlreadyExist($migrationFilePath);
        $this->createFromTemplate($migrationFilePath, $className);

        $output->writeln("<info>+f</info> $migrationFilePath");

        return 0;
    }

    protected function createFromTemplate(string $migrationFilePath, string $className): void
    {
        $contents = file_get_contents($this->configuration->template());
        $contents = preg_replace('/\{\{\s*className\s*\}\}/', $className, $contents);

        if (false === file_put_contents($migrationFilePath, $contents)) {
            throw new \RuntimeException(sprintf(
                'The file "%s" could not be written to',
                $migrationFilePath
            ));
        }
    }

    protected function validateMigrationName($migrationName): string
    {
        //http://php.net/manual/en/language.variables.basics.php
        if (preg_match('/^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*/', $migrationName)) {
            return $migrationName;
        }

        throw new \InvalidArgumentException("Invalid migration Name");
    }

    protected function assertMigrationFileDoesNotAlreadyExist(string $migrationFilePath): void
    {
        if (file_exists($migrationFilePath)) {
            throw new \InvalidArgumentException(sprintf(
                'The file "%s" already exists',
                $migrationFilePath
            ));
        }
    }
}
