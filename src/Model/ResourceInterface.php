<?php

namespace App\Model;

interface ResourceInterface
{
    /**
     * @return int|null
     */
    public function getId(): ?int;

    /**
     * @param int|null $id
     *
     * @return $this
     */
    public function setId(?int $id): self;
}