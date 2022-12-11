<?php

namespace App\Model;

interface TimestampableInterface
{
    /**
     * Set created at.
     *
     * @return TimestampableInterface
     */
    public function setCreatedAt(\DateTime $createdAt);

    /**
     * @return \DateTime
     */
    public function getCreatedAt();

    /**
     * Get updated at.
     *
     * @return TimestampableInterface
     */
    public function setUpdatedAt(\DateTime $updatedAt);

    /**
     * Get updated at.
     *
     * @return \DateTime
     */
    public function getUpdatedAt();
}
