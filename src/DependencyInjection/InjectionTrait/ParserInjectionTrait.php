<?php

namespace App\DependencyInjection\InjectionTrait;

use App\Parser\Parser;

trait ParserInjectionTrait
{
    /**
     * @var Parser
     */
    protected Parser $parser;

    /**
     * @required
     *
     * @param Parser $parser
     *
     * @return $this
     */
    public function setParser(Parser $parser): self
    {
        $this->parser = $parser;

        return $this;
    }
}