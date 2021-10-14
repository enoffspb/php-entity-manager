<?php

namespace enoffspb\EntityManager;

class EntityMetadata
{
    public string $entityClass;
    public ?string $repositoryClass = null;
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

    public function setMapping(array $mapping): void
    {
        $this->mapping = $mapping;
    }

    public function getPkValue(object $entity)
    {
        $pk = $this->primaryKey;
        $pkMap = $this->getMapping()[$pk] ?? null;

        $id = null;
        if($pkMap === null) {
            $id = $entity->$pk;
        } else {
            if(is_array($pkMap)) {
                $getter = $pkMap[0];
                $id = $entity->$getter();
            } else {
                $id = $entity->$pkMap;
            }
        }

        return $id;
    }

    public function setPkValue($entity, $idValue): void
    {
        $pkField = $this->primaryKey;
        $pkFieldMap = $this->getMapping()[$pkField] ?? null;
        if($pkFieldMap === null) {
            $entity->$pkField = $idValue;
        } else {
            if(is_array($pkFieldMap)) {
                $setter = $pkFieldMap[1];
                $entity->$setter($idValue);
            } else {
                $entity->$pkFieldMap = $idValue;
            }
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
