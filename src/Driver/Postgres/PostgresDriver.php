<?php

namespace EnoffSpb\EntityManager\Driver\Postgres;

use EnoffSpb\EntityManager\Driver\SqlBaseDriver;
use EnoffSpb\EntityManager\Interfaces\DriverInterface;

class PostgresDriver extends SqlBaseDriver implements DriverInterface
{
    public string $identifierQuote = '"';
    public string $valueQuote = "'";
}
