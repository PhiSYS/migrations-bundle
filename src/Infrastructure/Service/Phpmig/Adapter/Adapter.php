<?php
declare(strict_types=1);

namespace PhiSYS\MigrationsBundle\Infrastructure\Service\Phpmig\Adapter;

use Phpmig\Adapter\AdapterInterface;

interface Adapter extends AdapterInterface
{
    public function getConnection();
}
