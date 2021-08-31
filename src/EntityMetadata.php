<?php

namespace enoffspb\EntityManager;

class EntityMetadata
{
    public string $entityClass;
    public string $tableName;
    public string $primaryKey = 'id';

    private array $mapping = [];

    /**
     * @return Column[]
     */
    public function getMapping(): array
    {
        return $this->mapping;
    }

    public function createColumnsFromDescribe(array $describeRows)
    {
        foreach($describeRows as $row) {
            $column = new Column();
            $column->loadFromDescribe($row);
            $this->mapping[$column->name] = $column;
        }
    }
}
