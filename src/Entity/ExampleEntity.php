<?php

namespace App\Entity;

use App\Model\ResourceInterface;
use App\Model\ResourceTrait;
use App\Model\TimestampableInterface;
use App\Model\TimestampableTrait;
use App\Repository\TestEntityRepository;
use Doctrine\ORM\Mapping as ORM;


class ExampleEntity implements ResourceInterface, TimestampableInterface
{
    use ResourceTrait;
    use TimestampableTrait;

    /**
     * @var string
     */
    private string $name;

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return $this
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }
}
