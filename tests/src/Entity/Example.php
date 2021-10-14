<?php

namespace enoffspb\EntityManager\Tests\Entity;

class Example
{
    private ?int $id = null;
    public ?string $name = null;

    private ?string $custom = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id)
    {
        $this->id = $id;
    }

    public function getCustom(): ?string
    {
        return $this->custom;
    }

    public function setCustom(?sting $custom)
    {
        $this->custom = $custom;
    }
}
