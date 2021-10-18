<?php

namespace EnoffSpb\EntityManager\Driver\MySql;

use EnoffSpb\EntityManager\Driver\SqlAbstractDriver;
use EnoffSpb\EntityManager\Interfaces\DriverInterface;

class MySqlDriver extends SqlAbstractDriver implements DriverInterface
{
    public string $identifierQuote = '`';
    public string $valueQuote = "'";
}
