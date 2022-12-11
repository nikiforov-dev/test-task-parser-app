<?php

namespace App\Model;

interface ResourceInterface
{
    public function getId(): ?int;

    /**
     * @return $this
     */
    public function setId(?int $id): self;
}
