<?php

namespace App\DependencyInjection\InjectionTrait;

use Doctrine\ORM\EntityManager;

trait EntityManagerInjectionTrait
{
    /**
     * @var EntityManager
     */
    protected EntityManager $entityManager;

    /**
     * @required
     *
     * @param EntityManager $entityManager
     *
     * @return $this
     */
    public function setEntityManager(EntityManager $entityManager): self
    {
        $this->entityManager = $entityManager;

        return $this;
    }
}