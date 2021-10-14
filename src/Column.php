<?php

namespace enoffspb\EntityManager;

class Column
{
    const TYPE_INT = 'INT';
    const TYPE_BIGINT = 'BIGINT';
    const TYPE_SMALLINT = 'SMALLINT';
    const TYPE_TINYINT = 'TINYINT';
    const TYPE_VARCHAR = 'VARCHAR';
    const TYPE_TEXT = 'TEXT';

    public string $field;
    public string $type;
    public ?string $attribute;
    public ?string $getter;
    public ?string $setter;
    public ?int $length;
    public bool $nullable = false;

    public function isInteger(): bool
    {
        $isInteger = false;

        switch($this->type) {
            case self::TYPE_INT:
            case self::TYPE_BIGINT:
            case self::TYPE_SMALLINT:
            case self::TYPE_TINYINT:
                $isInteger = true;
                break;
        }

        return $isInteger;
    }

}
