<?php

namespace App\Entity;

use App\Model\ResourceInterface;
use App\Model\ResourceTrait;
use App\Model\TimestampableInterface;
use App\Model\TimestampableTrait;

class ExampleEntity implements ResourceInterface, TimestampableInterface
{
    use ResourceTrait;
    use TimestampableTrait;

    private string $name;

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return $this
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }
}
