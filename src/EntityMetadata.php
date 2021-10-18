<?php

namespace EnoffSpb\EntityManager;

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

    /**
     * @param object $entity
     * @return mixed
     */
    public function getPkValue(object $entity)
    {
        return $this->getFieldValue($entity, $this->primaryKey);
    }

    /**
     * @param object $entity
     * @param mixed $idValue
     */
    public function setPkValue(object $entity, $idValue): void
    {
        $this->setFieldValue($entity, $this->primaryKey, $idValue);
    }

    /**
     * @param object $entity
     * @param string $field
     * @param mixed $value
     */
    public function setFieldValue(object $entity, string $field, $value): void
    {
        $column = $this->getMapping()[$field] ?? null;
        if($column === null) {
            throw new \Exception('EntityMetadata(' . get_class($entity) . ') has not configured "' . $field . '" column.');
        }

        $id = null;
        if($column->setter) {
            $setter = $column->setter;
            $entity->$setter($value);
        } else {
            $attr = $column->attribute;
            $entity->$attr = $value;
        }
    }

    /**
     * @return mixed
     */
    public function getFieldValue(object $entity, string $field)
    {
        $column = $this->getMapping()[$field] ?? null;
        if($column === null) {
            throw new \Exception('EntityMetadata(' . get_class($entity) . ') has not configured "' . $field . '" column.');
        }

        if($column->getter) {
            $getter = $column->getter;
            return $entity->$getter();
        }

        $attr = $column->attribute;
        return $entity->$attr;
    }

    public function getValues(object $entity): array
    {
        $columns = $this->getMapping();

        $fields = [];
        $attribute = null;
        $value = null;

        foreach($columns as $column) {
            $getter = $column->getter;

            if($getter !== null) {
                $value = $entity->$getter();
            } else {
                $attribute = $column->attribute;
                $value = $entity->$attribute;
            }

            $fields[$column->field] = $value;
        }

        return $fields;
    }

    public function setValues(object $entity, array $values): void
    {
        $columns = $this->getMapping();

        foreach($columns as $column) {
            $setter = $column->setter;

            if($setter !== null) {
                $entity->$setter($values[$column->field]);
            } else {
                $attribute = $column->attribute;
                $entity->$attribute = $values[$column->field];
            }
        }
    }
}
