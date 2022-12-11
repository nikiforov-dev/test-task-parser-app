<?php

namespace App\DependencyInjection\InjectionTrait;

use Psr\Log\LoggerInterface;

trait LoggerInjectionTrait
{
    protected LoggerInterface $logger;

    /**
     * @required
     *
     * @return $this
     */
    public function setLogger(LoggerInterface $logger): self
    {
        $this->logger = $logger;

        return $this;
    }
}
