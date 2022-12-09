<?php

namespace App\Model;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

trait TimestampableTrait
{
    /**
     * @ORM\Column(name="created_at", type="datetime")
     */
    protected ?\DateTime $createdAt;

    /**
     * @ORM\Column(name="updated_at", type="datetime")
     */
    protected ?\DateTime $updatedAt;

    /**
     * @return $this
     */
    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @return $this
     */
    public function setUpdatedAt(\DateTime $updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @ORM\PrePersist()
     */
    public function timestampablePrePersist(): void
    {
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
    }

    /**
     * @ORM\PreUpdate()
     */
    public function timestampablePreUpdate(): void
    {
        $this->updatedAt = new \DateTime();
    }

    /**
     * @return null|int
     *
     * @psalm-suppress DocblockTypeContradiction
     * @psalm-suppress RedundantConditionGivenDocblockType
     */
    public function getCreatedAtTimestamp()
    {
        return $this->createdAt?->getTimestamp();
    }

    /**
     * @return null|int
     *
     * @psalm-suppress DocblockTypeContradiction
     * @psalm-suppress RedundantConditionGivenDocblockType
     */
    public function getUpdatedAtTimestamp()
    {
        return $this->updatedAt?->getTimestamp();
    }
}
