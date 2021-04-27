<?php
declare(strict_types=1);

namespace PhiSYS\MigrationsBundle\Infrastructure\Service\Phpmig\Adapter;

use Doctrine\DBAL\Connection;

class DbalAdapter extends \Phpmig\Adapter\Doctrine\DBAL implements Adapter
{
    public function getConnection(): Connection
    {
        return $this->connection;
    }
}
