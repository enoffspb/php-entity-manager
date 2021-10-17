<?php

namespace EnoffSpb\EntityManager\Driver\MySql;

use EnoffSpb\EntityManager\Driver\SqlAbstractDriver;

class MySqlDriver extends SqlAbstractDriver
{
    protected string $identifierQuote = '`';
    protected string $valueQuote = "'";
}
