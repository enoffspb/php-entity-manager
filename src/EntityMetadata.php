<?php

namespace enoffspb\EntityManager;

class EntityMetadata
{
    public string $entityClass;
    public ?string $repositoryClass = null;
    public string $tableName;
    public string $primaryKey = 'id';

    private array $mapping = [];

    public function __construct(array $config = [])
    {
        foreach($config as $k => $v) {
            if($k === 'mapping') {
                continue;
            }

            $this->$k = $v;
        }

        $mapping = $config['mapping'] ?? [];
        foreach($mapping as $fieldName => $columnConfig) {
            $column = new Column();
            $column->field = $fieldName;
            $column->type = $columnConfig['type'] ?? Column::TYPE_TEXT;
            $column->attribute = $columnConfig['attribute'] ?? null;
            $column->getter = $columnConfig['getter'] ?? null;
            $column->setter = $columnConfig['setter'] ?? null;
            $column->length = $columnConfig['length'] ?? null;
            if(isset($columnConfig['nullable'])) {
                $column->nullable = (bool) $columnConfig['nullable'];
            }

            if($column->attribute === null && ($column->getter === null || $column->setter === null)) {
                $column->attribute = $fieldName;
            }

            $this->mapping[$fieldName] = $column;
        }
    }

    /**
     * @return Column[]
     */
    public function getMapping(): array
    {
        return $this->mapping;
    }

    public function setMapping(array $mapping): void
    {
        $this->mapping = $mapping;
    }

    public function getPkValue(object $entity)
    {
        $pkColumn = $this->getMapping()[$this->primaryKey] ?? null;
        if($pkColumn === null) {
            throw new \Exception('EntityMetadata has not primaryKey column.');
        }

        $id = null;
        if($pkColumn->getter) {
            $getter = $pkColumn->getter;
            return $entity->$getter();
        }

        $attr = $pkColumn->attribute;
        return $entity->$attr;
    }

    public function setPkValue($entity, $idValue): void
    {
        $pkColumn = $this->getMapping()[$this->primaryKey] ?? null;
        if($pkColumn === null) {
            throw new \Exception('EntityMetadata has not primaryKey column.');
        }

        $id = null;
        if($pkColumn->setter) {
            $setter = $pkColumn->setter;
            $entity->$setter($idValue);
        } else {
            $attr = $pkColumn->attribute;
            $entity->$attr = $idValue;
        }
    }

//    public function createColumnsFromDescribe(array $describeRows)
//    {
//        foreach($describeRows as $row) {
//            $column = new Column();
//            $column->loadFromDescribe($row);
//            $this->mapping[$column->name] = $column;
//        }
//    }
}
