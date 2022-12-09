<?php

namespace App\Model;

trait ResourceTrait
{
    private ?int $id = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return ResourceTrait
     */
    public function setId(?int $id): self
    {
        $this->id = $id;

        return $this;
    }
}
