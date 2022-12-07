<?php

namespace App\Model;

trait ResourceTrait
{
    /**
     * @var int|null
     */
    private ?int $id = null;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int|null $id
     * @return ResourceTrait
     */
    public function setId(?int $id): self
    {
        $this->id = $id;

        return $this;
    }
}