<?php
declare(strict_types=1);

namespace DosFarma\MigrationsBundle\Command;

use DosFarma\MigrationsBundle\Infrastructure\Service\Phpmig\ConfigurationContainer;
use Phpmig\Migration\Migration;
use Phpmig\Migration\Migrator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\OutputInterface;

abstract class AbstractCommand extends Command
{
    protected ConfigurationContainer $configuration;
    private ?Migrator $migrator;

    public function __construct(ConfigurationContainer $configuration)
    {
        $this->configuration = $configuration;
        $this->migrator = null;

        parent::__construct();
    }

    protected function getMigrationsInDirectory(OutputInterface $output): array
    {
        $versions = [];
        $migrationFiles = \glob($this->configuration->migrationsDirectory() . \DIRECTORY_SEPARATOR . '*.php');

        foreach ($migrationFiles as $path) {
            [$version, $class] = $this->getVersionAndClassFromFileName(basename($path));
            include $path;
            $migration = new $class($version);

            if (!($migration instanceof Migration)) {
                throw new \InvalidArgumentException(\sprintf(
                    'The class "%s" in file "%s" must extend \Phpmig\Migration\Migration',
                    $class,
                    $path,
                ));
            }
            // inject output
            $migration->setOutput($output);

            $versions[$version] = $migration;
        }

        \ksort($versions);

        return $versions;
    }

    protected function getVersionsAlreadyDone(): array
    {
        $versions = $this->configuration->adapter()->fetchAll();
        \sort($versions);

        return $versions;
    }

    protected function getVersionAndClassFromFileName($migrationName): array
    {
        \preg_match('/^(\d+)_([a-zA-Z0-9_\x7f-\xff]+)/u', $migrationName, $matches);
        $version = $matches[1];
        $name = $matches[2];
        $class = \str_replace('_', ' ', $name);
        $class = \ucwords($class);
        $class = \str_replace(' ', '', $class) . "_" . $version;

        if (!$this->isValidClassName($class)) {
            throw new \InvalidArgumentException(\sprintf(
                'Migration class "%s" is invalid',
                $class,
            ));
        }

        return [$version, $class];
    }

    protected function validateClassName($migrationName): string
    {
        $class = \preg_replace('/[^\d\p{L}_]/u', ' ', $migrationName);
        $class = \ucwords($class);
        $class = \str_replace(' ', '', $class);

        if (!$this->isValidClassName($class)) {
            throw new \InvalidArgumentException(\sprintf(
                'Migration class "%s" is invalid',
                $class,
            ));
        }

        return $class;
    }

    protected function getMigrator(OutputInterface $output)
    {
        if (null === $this->migrator) {
            $this->migrator = new Migrator($this->configuration->adapter(), $this->configuration, $output);
        }

        return $this->migrator;
    }

    private function isValidClassName($className): bool
    {
        return 1 === \preg_match('/^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*$/u', $className);
    }
}
