<?php

namespace EnoffSpb\EntityManager\Tests\Entity;

class Example
{
    private ?int $id = null;
    public ?string $name = null;
    private int $order;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id)
    {
        $this->id = $id;
    }

    public function setOrder(int $order)
    {
        $this->order = $order;
    }

    public function getOrder(): ?int
    {
        return $this->order;
    }
}
