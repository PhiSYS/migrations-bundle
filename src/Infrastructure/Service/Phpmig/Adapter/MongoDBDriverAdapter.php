<?php
declare(strict_types=1);

namespace PhiSYS\MigrationsBundle\Infrastructure\Service\Phpmig\Adapter;

use MongoDB\Driver as MongoDBDriver;
use Phpmig\Migration\Migration;

class MongoDBDriverAdapter implements Adapter
{
    const INDEX_NAME = 'phpmig_versions';
    const FIELD_NAME = 'version';

    protected ?MongoDBDriver\Manager $manager = null;
    protected ?string $nameSpace = null;
    protected ?array $collectionSpec = null;

    public function __construct(MongoDBDriver\Manager $connection, string $collectionNameSpace)
    {
        $this->manager = $connection;
        $this->nameSpace = $collectionNameSpace;
        $this->collectionSpec = explode('.', $collectionNameSpace);
    }

    /** @throws MongoDBDriver\Exception\Exception */
    public function fetchAll(): array
    {
        $versions = [];
        $cursor = $this->manager->executeQuery(
            $this->nameSpace,
            new MongoDBDriver\Query([]),
        );

        if ($cursor instanceof MongoDBDriver\Cursor) {
            foreach ($cursor as $version) {
                $versions[] = $version->version;
            }
        }

        return $versions;
    }

    /** @throws MongoDBDriver\Exception\Exception */
    public function up(Migration $migration): self
    {
        $bulk = new MongoDBDriver\BulkWrite();
        $bulk->insert([self::FIELD_NAME => $migration->getVersion()]);
        $this->manager->executeBulkWrite($this->nameSpace, $bulk);

        return $this;
    }

    /** @throws MongoDBDriver\Exception\Exception */
    public function down(Migration $migration): self
    {
        $bulk = new MongoDBDriver\BulkWrite();
        $bulk->delete([self::FIELD_NAME => $migration->getVersion()], ['limit' => 1]);
        $this->manager->executeBulkWrite($this->nameSpace, $bulk);

        return $this;
    }

    /** @throws MongoDBDriver\Exception\Exception */
    public function hasSchema(): bool
    {
        $cursor = $this->manager->executeQuery(
            $this->nameSpace,
            new MongoDBDriver\Query([], ['limit' => 1]),
        );

        foreach ($cursor as $document) {
            return true;
        }

        try {
            $indexesCursor = $this->executeMongoCommand(
                $this->getMongoCommand(
                    [
                        'listIndexes' => \implode('.', \array_slice($this->collectionSpec, 1)),
                    ],
                ),
            );

            foreach ($indexesCursor as $key) {
                if (self::INDEX_NAME === $key->name) {
                    return true;
                }
            }
        } catch (MongoDBDriver\Exception\Exception $exception) {
            // Do nothing if it doesn't exist
        }

        return false;
    }

    public function createSchema(): self
    {
        $collectionName = \implode('.', \array_slice($this->collectionSpec, 1));
        $this->executeMongoCommand($this->getMongoCommand([
            'create' => $collectionName,
        ]));
        $this->executeMongoCommand($this->getMongoCommand([
            'createIndexes' => $collectionName,
            'indexes' => [
                [
                    'key' => [
                        self::FIELD_NAME => 1,
                    ],
                    'name' => self::INDEX_NAME,
                    'unique' => true,
                ],
            ],
        ]));

        return $this;
    }

    public function getConnection(): MongoDBDriver\Manager
    {
        return $this->manager;
    }

    protected function getMongoCommand(array $command): MongoDBDriver\Command
    {
        return new MongoDBDriver\Command($command);
    }

    protected function executeMongoCommand(MongoDBDriver\Command $mongoCommand): MongoDBDriver\Cursor
    {
        return $this->manager->executeCommand($this->collectionSpec[0], $mongoCommand);
    }
}
