<?php

namespace EnoffSpb\EntityManager\Driver\MySql;

use EnoffSpb\EntityManager\Driver\SqlBaseDriver;
use EnoffSpb\EntityManager\Interfaces\DriverInterface;

class MySqlDriver extends SqlBaseDriver implements DriverInterface
{
    public string $identifierQuote = '`';
    public string $valueQuote = "'";
}
