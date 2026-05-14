<?php

declare(strict_types=1);

namespace App\Modules\Maestros\Repositories;

use App\Core\Database;
use PDO;

abstract class BaseMaestroRepository
{
    protected function pdo(): PDO
    {
        return Database::getInstance()->getConnection();
    }
}
