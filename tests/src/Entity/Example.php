<?php

namespace enoffspb\EntityManager\Tests\Entity;

class Example
{
    private ?int $id = null;
    public ?string $name = null;

    private ?string $private = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id)
    {
        $this->id = $id;
    }

    public function getPrivate(): ?string
    {
        return $this->private;
    }

    public function setPrivate(?sting $private)
    {
        $this->private = $private;
    }
}
