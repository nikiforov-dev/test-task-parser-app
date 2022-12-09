<?php

namespace App\DependencyInjection\InjectionTrait;

use Doctrine\ORM\EntityManager;

trait EntityManagerInjectionTrait
{
    protected EntityManager $entityManager;

    /**
     * @required
     *
     * @return $this
     */
    public function setEntityManager(EntityManager $entityManager): self
    {
        $this->entityManager = $entityManager;

        return $this;
    }
}
