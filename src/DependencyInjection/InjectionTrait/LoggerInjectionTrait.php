<?php

namespace App\DependencyInjection\InjectionTrait;

use Psr\Log\LoggerInterface;

trait LoggerInjectionTrait
{
    /**
     * @var LoggerInterface
     */
    protected LoggerInterface $logger;

    /**
     * @required
     *
     * @param LoggerInterface $logger
     *
     * @return $this
     */
    public function setLogger(LoggerInterface $logger): self
    {
        $this->logger = $logger;

        return $this;
    }
}