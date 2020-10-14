<?php
declare(strict_types=1);

namespace DosFarma\MigrationsBundle\Infrastructure\Service\Phpmig;

use DosFarma\MigrationsBundle\Infrastructure\Service\Phpmig\Adapter\Adapter;

class ConfigurationContainer implements \ArrayAccess
{
    private array $store = [];

    private function __construct()
    {
        // Disable public constructor
    }

    public static function from(Adapter $adapter, string $directory, string $template): self
    {
        $instance = new self();
        $instance->store['migrations_path'] = self::validateDirectoryPath($directory);
        $instance->store['phpmig.adapter'] = $adapter;
        $instance->store['phpmig.migrations_template_path'] = self::validateTemplatePath($template);
        $instance->store['db'] = $adapter->getConnection();

        return $instance;
    }

    public function offsetExists($key)
    {
        return \array_key_exists($key, $this->store);
    }

    public function offsetGet($key)
    {
        return $this->store[$key];
    }

    public function offsetSet($key, $value)
    {
        $this->store[$key] = $value;
    }

    public function offsetUnset($key)
    {
        unset($this->store[$key]);
    }

    public function adapter(): Adapter
    {
        return $this->store['phpmig.adapter'];
    }

    public function connection()
    {
        return $this->store['db'];
    }

    public function migrationsDirectory(): string
    {
        return $this->store['migrations_path'];
    }

    public function template(): string
    {
        return $this->store['phpmig.migrations_template_path'];
    }

    private static function validateDirectoryPath(string $directory): string
    {
        $info = new \SplFileInfo($directory);

        if ($info->isDir() && $info->isReadable() && $info->isWritable()) {
            return $info->getRealPath();
        }

        throw new \InvalidArgumentException(\sprintf("Invalid migrations directory '%s'", $directory));
    }

    private static function validateTemplatePath(string $template): string
    {
        $info = new \SplFileInfo($template);

        if ($info->isFile() && $info->isReadable()) {
            return $info->getRealPath();
        }

        throw new \InvalidArgumentException(\sprintf("Invalid template file '%s'", $template));
    }
}
